<?php namespace Groupeat\Support\Api\V1;

use Aws\Ec2\Exception\Ec2Exception;
use Dingo\Api\Routing\ControllerTrait as ApiController;
use Groupeat\Support\Exceptions\Exception;
use Illuminate\Routing\Controller as IlluminateController;
use Illuminate\Support\Collection;
use Input;
use League\Fractal\TransformerAbstract;

abstract class Controller extends IlluminateController {

    use ApiController;


    /**
     * @param $item
     *
     * @return \Dingo\Api\Http\ResponseBuilder
     */
    protected function itemResponse($item)
    {
        if (empty($item))
        {
            throw new Exception(
                'cannotMakeResponseFromEmptyItem',
                "Cannot make response from empty item."
            );
        }

        return $this->response->item($item, $this->getTransformerFor($item));
    }

    /**
     * @param Collection $collection
     * @param TransformerAbstract $transfomer
     *
     * @return \Dingo\Api\Http\ResponseBuilder
     */
    protected function collectionResponse(Collection $collection, TransformerAbstract $transfomer = null)
    {
        if (is_null($transfomer))
        {
            if ($collection->isEmpty())
            {
                throw new Exception(
                    'cannotFindTransformerFromEmptyCollection',
                    "Cannot find transfomer from empty collection."
                );
            }

            $transfomer = $this->getTransformerFor($collection->first());
        }

        return $this->response->collection($collection, $transfomer);
    }

    /**
     * @param $data
     *
     * @return \Dingo\Api\Http\ResponseBuilder
     */
    protected function arrayResponse($data)
    {
        return $this->response->array(compact('data'));
    }

    /**
     * @param $relation
     *
     * @return bool
     */
    protected function shouldInclude($relation)
    {
        return str_contains(Input::get('include'), $relation);
    }

    /**
     * @param $item
     *
     * @return TransformerAbstract
     */
    protected function getTransformerFor($item)
    {
        $className = $this->getTransformerClassNameFor($item);

        return new $className;
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
