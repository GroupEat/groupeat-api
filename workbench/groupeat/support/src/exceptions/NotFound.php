<?php namespace Groupeat\Support\Exceptions;

use Exception as BaseException;
use Symfony\Component\HttpFoundation\Response;

class NotFound extends Exception {

    public function __construct(
        $errorKey,
        $message = null,
        $errors = null,
        array $headers = [],
        BaseException $previous = null,
        $code = 0
    )
    {
        Exception::__construct($errorKey, $message, Response::HTTP_NOT_FOUND, $errors, $headers, $previous, $code);
    }

}
