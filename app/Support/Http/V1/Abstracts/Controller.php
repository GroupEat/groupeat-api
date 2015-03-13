<?php
namespace Groupeat\Support\Http\V1\Abstracts;

use Groupeat\Support\Http\Output;
use Groupeat\Support\Exceptions\Exception;
use Illuminate\Routing\Controller as IlluminateController;
use Illuminate\Support\Collection;
use League\Fractal\TransformerAbstract;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller extends IlluminateController
{
    protected $statusCode = Response::HTTP_OK;

    /**
     * @var Output
     */
    private $output;

    /**
     * @param Output $output
     */
    public function __construct(Output $output)
    {
        $this->output = $output;
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
        $itemClass = getClassNameWithoutNamespace($item);

        return $controllerNamespace.'\\'.$itemClass.'Transformer';
    }
}
