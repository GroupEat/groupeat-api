<?php
namespace Groupeat\Http\Responses;

use League\Fractal\Manager as Fractal;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class Output
{
    /**
     * Fractal library used for data presention and transformation.
     *
     * @var Fractal
     */
    private $fractal;

    /**
     * Construct a new output manager with a fractal manager.
     *
     * @param Fractal $fractal
     */
    public function __construct(Fractal $fractal)
    {
        $this->fractal = $fractal;
    }

    /**
     * Output a single item.
     *
     * @param  mixed $item
     * @param  mixed $callback
     *
     * @return array
     */
    public function asItemArray($item, $callback)
    {
        $resource = new Item($item, $callback);

        if (\Request::get('include')) {
            $this->fractal->parseIncludes(\Request::get('include'));
        }

        $root = $this->fractal->createData($resource);

        return $root->toArray();
    }

    /**
     * Output as a collection of items.
     *
     * @param  mixed $collection
     * @param  mixed $callback
     *
     * @return array
     */
    public function asCollectionArray($collection, $callback)
    {
        if (\Request::get('include')) {
            $this->fractal->parseIncludes(\Request::get('include'));
            $collection->load($this->fractal->getRequestedIncludes());
        }

        $resource = new Collection($collection, $callback);

        $root = $this->fractal->createData($resource);

        return $root->toArray();
    }
}
