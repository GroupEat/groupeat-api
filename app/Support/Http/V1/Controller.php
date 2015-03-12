<?php
namespace Groupeat\Support\Http\V1;

use Groupeat\Http\Controllers\ApiController;
use Groupeat\Support\Exceptions\Exception;
use Illuminate\Support\Collection;
use League\Fractal\TransformerAbstract;

abstract class Controller extends ApiController
{
    /**
     * @param                     $item
     * @param TransformerAbstract $transformer
     *
     * @return \Illuminate\Http\Response
     */
    protected function itemResponse($item, TransformerAbstract $transformer = null)
    {
        if (empty($item)) {
            throw new Exception(
                'cannotMakeResponseFromEmptyItem',
                "Cannot make response from empty item."
            );
        }

        if (is_null($transformer)) {
            $transformer = $this->getTransformerFor($item);
        }

        return $this->respondWithItem($item, $transformer);

        //return $this->response->item($item, $this->getTransformerFor($item));
    }

    /**
     * @param Collection          $collection
     * @param TransformerAbstract $transfomer
     *
     * @return \Illuminate\Http\Response
     */
    protected function collectionResponse(Collection $collection, TransformerAbstract $transfomer = null)
    {
        if (is_null($transfomer)) {
            if ($collection->isEmpty()) {
                throw new Exception(
                    'cannotFindTransformerFromEmptyCollection',
                    "Cannot find transfomer from empty collection."
                );
            }

            $transfomer = $this->getTransformerFor($collection->first());
        }

        return $this->respondWithCollection($collection, $transfomer);

        //return $this->response->collection($collection, $transfomer);
    }

    /**
     * @param $data
     *
     * @return \Illuminate\Http\Response
     */
    protected function arrayResponse($data)
    {
        return $this->respondWithArray(compact('data'));

       // return $this->response->array(compact('data'));
    }

    /**
     * @param $item
     *
     * @return TransformerAbstract
     */
    protected function getTransformerFor($item)
    {
        $className = $this->getTransformerClassNameFor($item);

        return new $className();
    }

    /**
     * @param $item
     *
     * @return string
     */
    protected function getTransformerClassNameFor($item)
    {
        $controllerNamespace = getNamespaceOf($this);
        $itemClass = getClassNameWithoutNamespace($item);

        return $controllerNamespace.'\\'.$itemClass.'Transformer';
    }
}
