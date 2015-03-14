<?php
namespace Groupeat\Support\Http;

use Illuminate\Http\Request;
use League\Fractal\Manager as Fractal;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class Output
{
    private $fractal;
    private $request;

    public function __construct(Fractal $fractal, Request $request)
    {
        $this->fractal = $fractal;
        $this->request = $request;
    }

    public function asItemArray($item, $callback)
    {
        $resource = new Item($item, $callback);

        if ($this->request->get('include')) {
            $this->fractal->parseIncludes($this->request->get('include'));
        }

        $root = $this->fractal->createData($resource);

        return $root->toArray();
    }

    public function asCollectionArray($collection, $callback)
    {
        if ($this->request->get('include')) {
            $this->fractal->parseIncludes($this->request->get('include'));
            $collection->load($this->fractal->getRequestedIncludes());
        }

        $resource = new Collection($collection, $callback);

        $root = $this->fractal->createData($resource);

        return $root->toArray();
    }
}
