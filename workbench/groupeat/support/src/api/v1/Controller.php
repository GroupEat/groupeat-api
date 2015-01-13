<?php namespace Groupeat\Support\Api\V1;

use Dingo\Api\Routing\ControllerTrait as ApiController;
use Illuminate\Routing\Controller as IlluminateController;

abstract class Controller extends IlluminateController {

    use ApiController;


    /**
     * @param $item
     *
     * @return \Dingo\Api\Http\ResponseBuilder
     */
    protected function itemResponse($item)
    {
        return $this->response->item($item, $this->getTransformerFor($item));
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
     * @param $item
     *
     * @return \League\Fractal\TransformerAbstract
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
