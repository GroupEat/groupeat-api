<?php
namespace Groupeat\Support\Http\V1\Abstracts;

use Closure;
use Dingo\Api\Routing\Helpers;
use Groupeat\Auth\Auth;
use Groupeat\Support\Exceptions\BadRequest;
use Groupeat\Support\Jobs\Abstracts\Job;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as IlluminateController;
use Illuminate\Support\Collection;
use League\Fractal\TransformerAbstract;
use Symfony\Component\HttpFoundation\ParameterBag;
use Error;

abstract class Controller extends IlluminateController
{
    use Helpers;

    protected $request;
    protected $auth;
    private $dispatcher;

    public function __construct(Auth $auth, Dispatcher $dispatcher)
    {
        $this->auth = $auth;
        $this->dispatcher = $dispatcher;
    }

    protected function get(string $key, $default = null, bool $deep = false)
    {
        return $this->request()->query->get($key, $default, $deep);
    }

    protected function query(): ParameterBag
    {
        return $this->request()->query;
    }

    protected function allJson()
    {
        return $this->request()->json()->all();
    }

    protected function json(string $key)
    {
        $value = $this->request()->json($key);

        if (is_null($value)) {
            throw new BadRequest(
                'missingAttribute',
                "The attribute '$key' is missing from the request body"
            );
        }

        return $value;
    }

    protected function optionalJson(string $key = null, $default = null)
    {
        return $this->request()->json($key, $default);
    }

    protected function itemResponse($item, TransformerAbstract $transformer = null): Response
    {
        if (empty($item)) {
            return $this->response()->noContent();
        }

        if (is_null($transformer)) {
            $transformer = $this->getTransformerFor($item);
        }

        return $this->response()->item($item, $transformer);
    }

    protected function collectionResponse(Collection $collection, TransformerAbstract $transfomer = null): Response
    {
        if (is_null($transfomer)) {
            if ($collection->isEmpty()) {
                return $this->arrayResponse([]);
            }

            $transfomer = $this->getTransformerFor($collection->first());
        }

        return $this->response()->collection($collection, $transfomer);
    }

    protected function arrayResponse(array $data): Response
    {
        return $this->response()->array(compact('data'));
    }

    protected function getTransformerFor($item): TransformerAbstract
    {
        $className = $this->getTransformerClassNameFor($item);

        return new $className;
    }

    protected function getTransformerClassNameFor($item): string
    {
        $controllerNamespace = getNamespaceOf($this);
        $itemClass = class_basename($item);

        return $controllerNamespace.'\\'.$itemClass.'Transformer';
    }

    protected function makeJob(string $jobClass, ...$jobConstructorArguments)
    {
        try {
            return new $jobClass(...$jobConstructorArguments);
        } catch (Error $e) {
            throw new BadRequest(
                'cannotInstantiateJob',
                "Unable to instantiate job '$jobClass' with given request format. The catched error was: {$e->getMessage()}"
            );
        }
    }

    protected function throwBadRequestOnError(Closure $func)
    {
        try {
            return $func();
        } catch (Error $e) {
            throw new BadRequest(
                'badRequestFormat',
                "The request format may be invalid because the following error occured: {$e->getMessage()}"
            );
        }
    }

    protected function dispatch(Job $job)
    {
        return $this->dispatcher->dispatch($job);
    }

    protected function request()
    {
        return app('request');
    }
}
