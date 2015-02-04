<?php namespace Groupeat\Orders\Support;

use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Presenters\ProductFormats as ProductFormatsPresenter;
use Groupeat\Restaurants\Entities\ProductFormat;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Exceptions\Exception;
use Groupeat\Support\Exceptions\NotFound;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Database\Eloquent\Collection;

class ProductFormats {

    /**
     * @var array
     */
    private $amounts;

    /**
     * @var Collection
     */
    private $models;

    /**
     * @var Restaurant
     */
    private $restaurant;

    /**
     * @var int
     */
    private $totalNumberOfProductFormats;

    /**
     * @var float
     */
    private $totalPrice;


    /**
     * @param string $json
     *
     * @return static
     */
    public static function fromJSON($json)
    {
        return new static(decodeJSON($json));
    }

    /**
     * @param array      $amounts
     * @param Collection $models
     * @param Restaurant $restaurant
     */
    private function __construct(array $amounts, Collection $models = null, Restaurant $restaurant = null)
    {
        if (empty($amounts) || (array_sum($amounts) == 0))
        {
            throw new UnprocessableEntity(
                'noProductFormats',
                "There must be at least one product format."
            );
        }

        $this->amounts = $amounts;

        $askedIds = $this->getIds();

        if (is_null($models))
        {
            $this->models = ProductFormat::with('product.restaurant')->findMany($askedIds);
        }
        else
        {
            $this->models = $models;
        }

        $foundIds = $this->models->lists('id');
        $missingIds = array_diff($askedIds, $foundIds);

        if (!empty($missingIds))
        {
            throw new NotFound(
                'unexistingProductFormats',
                "The product formats #" . implode(',', $missingIds) . " do not exist."
            );
        }

        if (is_null($restaurant))
        {
            $restaurants = $this->models->map(function($productFormat)
            {
                return $productFormat->product->restaurant;
            });

            if ($restaurants->unique()->count() > 1)
            {
                $this->throwNotSameRestaurantException();
            }

            $this->restaurant = $restaurants->first();
        }
        else
        {
            $this->restaurant = $restaurant;
        }

        $this->totalNumberOfProductFormats = array_sum($this->amounts);

        $this->totalPrice = round($this->models->sum(function(ProductFormat $productFormat)
        {
            return $this->amounts[$productFormat->id] * $productFormat->price;
        }), 2);
    }

    public function getAmounts()
    {
        return $this->amounts;
    }

    public function getIds()
    {
        return array_keys($this->amounts);
    }

    public function getModels()
    {
        return $this->models;
    }

    public function getRestaurant()
    {
        return $this->restaurant;
    }

    public function count()
    {
        return $this->totalNumberOfProductFormats;
    }

    public function price()
    {
        return $this->totalPrice;
    }

    public function attachTo(Order $order)
    {
        if (!$order->exists)
        {
            throw new Exception(
                'orderIdNeededToAttachProductFormats',
                "The order ID is needed to attach the product formats."
            );
        }

        $syncData = [];

        foreach ($this->amounts as $id => $amount)
        {
            $syncData[(int) $id] = ['amount' => (int) $amount];
        }

        $order->productFormats()->sync($syncData);
    }

    public function mergeWith(GroupOrder $groupOrder)
    {
        if ($groupOrder->restaurant->id != $this->restaurant->id)
        {
            $this->throwNotSameRestaurantException();
        }

        $amounts = $this->getAmounts();
        $models = $this->getModels();
        $groupOrder->load('orders.productFormats');

        foreach ($groupOrder->orders as $order)
        {
            foreach ($order->productFormats as $productFormat)
            {
                $formatId = $productFormat->id;
                $amount = $productFormat->pivot->amount;

                if ($models->contains($formatId))
                {
                    $amounts[$formatId] += $amount;
                }
                else
                {
                    $models->add($productFormat);
                    $amounts[$formatId] = $amount;
                }
            }
        }

        return new static($amounts, $models, $groupOrder->restaurant);
    }

    private function throwNotSameRestaurantException()
    {
        throw new UnprocessableEntity(
            'productFormatsFromDifferentRestaurants',
            "The product formats must belong to the same restaurant."
        );
    }

}
