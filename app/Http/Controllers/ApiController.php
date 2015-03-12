<?php
namespace Groupeat\Http\Controllers;

use Groupeat\Http\Responses\Output;
use Illuminate\Http\Response;
use UnexpectedValueException;

class ApiController extends Controller
{
    /**
     * An output class for sending expected outputs.
     *
     * @var Output
     */
    private $output;

    /**
     * Current status code of the given request.
     *
     * @var integer
     */
    protected $statusCode = Response::HTTP_OK;

    /**
     * Make a new api controller with an output class.
     *
     * @param Output $output
     */
    public function __construct(Output $output)
    {
        $this->output = $output;
    }

    /**
     * Get the current status code.
     *
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Respond with a json array.
     *
     * @param  array  $array
     * @param  array  $headers
     *
     * @return Response
     */
    protected function respondWithArray(array $array, array $headers = array())
    {
        return response()->json($array, $this->statusCode, $headers);
    }

    /**
     * Respond with a single item.
     *
     * @param  mixed $item
     * @param  mixed $callback
     *
     * @return Response
     */
    protected function respondWithItem($item, $callback)
    {
        $out = $this->output->asItemArray($item, $callback);

        return $this->respondWithArray($out)->setStatusCode($this->statusCode);
    }

    /**
     * Respond with a collection of items.
     *
     * @param  array $collection
     * @param  mixed $callback
     *
     * @return Response
     */
    protected function respondWithCollection($collection, $callback)
    {
        $out = $this->output->asCollectionArray($collection, $callback);

        return $this->respondWithArray($out)->setStatusCode($this->statusCode);
    }
}
