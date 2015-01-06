<?php namespace Groupeat\Support\Exceptions;

use Dingo\Api\Exception\ResourceException;
use Exception as BaseException;
use Illuminate\Support\MessageBag as Bag;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Exception extends ResourceException {

    public function __construct(
        $message = null,
        $statusCode = 500,
        $errors = null,
        array $headers = [],
        BaseException $previous = null,
        $code = 0
    )
    {
        if (is_null($errors))
        {
            $this->errors = new Bag;
        }
        else
        {
            $this->errors = is_array($errors) ? new Bag($errors) : $errors;
        }

        HttpException::__construct($statusCode, $message, $previous, $headers, $code);
    }

}
