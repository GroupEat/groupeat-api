<?php
namespace Groupeat\Support\Http\V1\Abstracts;

use Dingo\Api\Routing\Helpers;
use Groupeat\Auth\Auth;
use Groupeat\Support\Jobs\Abstracts\Job;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as IlluminateController;
use Illuminate\Support\Collection;
use League\Fractal\TransformerAbstract;

abstract class Controller extends IlluminateController
{
    use Helpers;

    protected $request;
    protected $auth;
    private $dispatcher;

    /**
     * @param Request    $request
     * @param Auth       $auth
     * @param Dispatcher $dispatcher
     */
    public function __construct(Request $request, Auth $auth, Dispatcher $dispatcher)
    {
        $this->request = $request;
        $this->auth = $auth;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param string $key
     * @param mixed  $default
     * @param bool   $deep
     *
     * @return mixed
     */
    protected function get($key, $default = null, $deep = false)
    {
        return $this->request->query->get($key, $default, $deep);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected function query()
    {
        return $this->request->query;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function json($key = null, $default = null)
    {
        return $this->request->json($key, $default);
    }

    /**
     * @param                     $item
     * @param TransformerAbstract $transformer
     *
     * @return \Illuminate\Http\Response
     */
    protected function itemResponse($item, TransformerAbstract $transformer = null)
    {
        if (empty($item)) {
            return $this->response()->noContent();
        }

        if (is_null($transformer)) {
            $transformer = $this->getTransformerFor($item);
        }

        return $this->response()->item($item, $transformer);
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
                return $this->arrayResponse([]);
            }

            $transfomer = $this->getTransformerFor($collection->first());
        }

        return $this->response()->collection($collection, $transfomer);
    }

    /**
     * @param array $data
     *
     * @return \Illuminate\Http\Response
     */
    protected function arrayResponse(array $data)
    {
        return $this->response()->array(compact('data'));
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
        $itemClass = class_basename($item);

        return $controllerNamespace.'\\'.$itemClass.'Transformer';
    }

    /**
     * @param Job $job
     *
     * @return mixed
     */
    protected function dispatch(Job $job)
    {
        return $this->dispatcher->dispatch($job);
    }
}
