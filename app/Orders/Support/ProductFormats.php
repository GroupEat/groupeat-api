<?php
namespace Groupeat\Orders\Support;

use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Restaurants\Entities\ProductFormat;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Exceptions\BadRequest;
use Groupeat\Support\Exceptions\Exception;
use Groupeat\Support\Exceptions\NotFound;
use Illuminate\Database\Eloquent\Collection;
use SebastianBergmann\Money\Money;

class ProductFormats
{
    private $quantities;
    private $models;
    private $restaurant;
    private $totalNumberOfProductFormats;
    private $totalPrice;

    public function __construct(array $quantities, Collection $models = null, Restaurant $restaurant = null)
    {
        $quantities = array_filter($quantities, function ($quantity) {
            return $quantity > 0;
        });

        if (empty($quantities) || (array_sum($quantities) == 0)) {
            throw new BadRequest(
                'missingProductFormats',
                "There must be at least one product format."
            );
        }

        $this->quantities = $quantities;

        $askedIds = $this->getIds();

        if (is_null($models)) {
            $this->models = ProductFormat::with('product.restaurant')->findMany($askedIds);
        } else {
            $this->models = $models;
        }

        $foundIds = $this->models->pluck('id')->all();
        $missingIds = array_diff($askedIds, $foundIds);

        if (!empty($missingIds)) {
            throw new NotFound(
                'unexistingProductFormats',
                "The product formats #".implode(',', $missingIds)." do not exist."
            );
        }

        if (is_null($restaurant)) {
            $restaurants = $this->models->map(function ($productFormat) {
                return $productFormat->product->restaurant;
            });

            if ($restaurants->unique()->count() > 1) {
                $this->throwNotSameRestaurantException();
            }

            $this->restaurant = $restaurants->first();
        } else {
            $this->restaurant = $restaurant;
        }

        $this->totalNumberOfProductFormats = array_sum($this->quantities);

        $this->totalPrice = sumPrices($this->models->map(function (ProductFormat $productFormat) {
            return $productFormat->price->multiply($this->quantities[$productFormat->id]);
        }));
    }

    public function getQuantities(): array
    {
        return $this->quantities;
    }

    public function getIds(): array
    {
        return array_keys($this->quantities);
    }

    public function getModels(): Collection
    {
        return $this->models;
    }

    public function getRestaurant(): Restaurant
    {
        return $this->restaurant;
    }

    public function count(): int
    {
        return $this->totalNumberOfProductFormats;
    }

    public function totalPrice(): Money
    {
        return $this->totalPrice;
    }

    public function attachTo(Order $order)
    {
        if (!$order->exists) {
            throw new Exception(
                'orderIdNeededToAttachProductFormats',
                "The order ID is needed to attach the product formats."
            );
        }

        $syncData = [];

        foreach ($this->quantities as $id => $quantity) {
            $syncData[(int) $id] = ['quantity' => (int) $quantity];
        }

        $order->productFormats()->sync($syncData);
    }

    public function mergeWith(GroupOrder $groupOrder)
    {
        if ($groupOrder->restaurant->id != $this->restaurant->id) {
            $this->throwNotSameRestaurantException();
        }

        $quantities = $this->getQuantities();
        $models = $this->getModels();
        $groupOrder->load('orders.productFormats');

        foreach ($groupOrder->orders as $order) {
            foreach ($order->productFormats as $productFormat) {
                $formatId = $productFormat->id;
                $quantity = $productFormat->pivot->quantity;

                if ($models->contains($formatId)) {
                    $quantities[$formatId] += $quantity;
                } else {
                    $models->add($productFormat);
                    $quantities[$formatId] = $quantity;
                }
            }
        }

        return new static($quantities, $models, $groupOrder->restaurant);
    }

    private function throwNotSameRestaurantException()
    {
        throw new BadRequest(
            'productFormatsFromDifferentRestaurants',
            "The product formats must belong to the same restaurant."
        );
    }
}
