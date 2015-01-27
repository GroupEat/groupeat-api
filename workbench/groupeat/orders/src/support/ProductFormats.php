<?php namespace Groupeat\Orders\Support;

use Groupeat\Orders\Entities\Order;
use Groupeat\Restaurants\Entities\ProductFormat;
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
     * @var \Groupeat\Restaurants\Entities\Restaurant
     */
    private $restaurant;


    /**
     * @param string $json
     *
     * @return static
     */
    public static function fromJSON($json)
    {
        return new static(decodeJSON($json));
    }

    public function __construct(array $amounts)
    {
        $this->amounts = $amounts;

        $this->assertNotEmpty();
        $this->setModels();
        $this->setRestaurant();
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

    private function assertNotEmpty()
    {
        if (empty($this->amounts) || (array_sum($this->amounts) == 0))
        {
            throw new UnprocessableEntity(
                'noProductFormats',
                "There must be at least one product format."
            );
        }
    }

    private function setModels()
    {
        $askedIds = $this->getIds();
        $models = ProductFormat::with('product.restaurant')->findMany($askedIds);

        $this->assertAllFormatsExist($models, $askedIds);

        $this->models = $models;
    }

    private function setRestaurant()
    {
        $restaurants = $this->models->map(function($productFormat)
        {
            return $productFormat->product->restaurant;
        })->unique();

        if ($restaurants->count() > 1)
        {
            throw new UnprocessableEntity(
                'productFormatsFromDifferentRestaurants',
                "The product formats must belong to the same restaurant."
            );
        }

        $this->restaurant = $restaurants->first();
    }

    private function assertAllFormatsExist(Collection $models, array $askedIds)
    {
        $foundIds = $models->lists('id');
        $missingIds = array_diff($askedIds, $foundIds);

        if (!empty($missingIds))
        {
            throw new NotFound(
                'unexistingProductFormats',
                "The product formats #" . implode(',', $missingIds) . " do not exist."
            );
        }
    }

}
