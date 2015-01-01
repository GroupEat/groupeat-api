<?php namespace Groupeat\Support\Exceptions;

use Exception as BaseException;
use Illuminate\Support\MessageBag as Bag;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Exception extends RuntimeException implements HttpExceptionInterface {

    /**
     * @var Bag
     */
    protected $errors;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var array
     */
    protected $headers;


    public function __construct(
        $message = null,
        $statusCode = 500,
        $errors = null,
        array $headers = [],
        BaseException $previous = null,
        $code = 0
    )
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;

        if (is_null($errors))
        {
            $this->errors = new Bag;
        }
        else
        {
            $this->errors = is_array($errors) ? new Bag($errors) : $errors;
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return Bag
     */
    public function errors()
    {
        return $this->getErrors();
    }

    /**
     * @return Bag
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return !$this->errors->isEmpty();
    }

}
