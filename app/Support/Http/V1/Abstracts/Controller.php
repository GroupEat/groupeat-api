<?php
namespace Groupeat\Support\Http\V1\Abstracts;

use Groupeat\Auth\Auth;
use Groupeat\Support\Commands\Abstracts\Command;
use Groupeat\Support\Exceptions\Exception;
use Groupeat\Support\Http\Output;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as IlluminateController;
use Illuminate\Support\Collection;
use League\Fractal\TransformerAbstract;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller extends IlluminateController
{
    /**
     * @var int
     */
    protected $statusCode = Response::HTTP_OK;

    protected $request;
    protected $auth;
    private $output;
    private $dispatcher;

    /**
     * @param Request    $request
     * @param Auth       $auth
     * @param Output     $output
     * @param Dispatcher $dispatcher
     */
    public function __construct(Request $request, Auth $auth, Output $output, Dispatcher $dispatcher)
    {
        $this->request = $request;
        $this->auth = $auth;
        $this->output = $output;
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
            throw new Exception(
                'cannotMakeResponseFromEmptyItem',
                "Cannot make response from empty item."
            );
        }

        if (is_null($transformer)) {
            $transformer = $this->getTransformerFor($item);
        }

        $out = $this->output->asItemArray($item, $transformer);

        return $this->arrayResponse($out)->setStatusCode($this->statusCode);
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

        $out = $this->output->asCollectionArray($collection, $transfomer);

        return $this->arrayResponse($out)->setStatusCode($this->statusCode);
    }

    /**
     * @param array $data
     *
     * @return \Illuminate\Http\Response
     */
    protected function arrayResponse(array $data)
    {
        return response()->json($data, $this->statusCode);
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
        $itemClass = class_basename($item);

        return $controllerNamespace.'\\'.$itemClass.'Transformer';
    }

    /**
     * @param Command $command
     *
     * @return mixed
     */
    protected function dispatch(Command $command)
    {
        return $this->dispatcher->dispatch($command);
    }
}
