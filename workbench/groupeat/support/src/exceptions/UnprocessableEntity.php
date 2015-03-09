<?php
namespace Groupeat\Support\Exceptions;

use Exception as BaseException;
use Symfony\Component\HttpFoundation\Response;

class UnprocessableEntity extends Exception
{
    public function __construct(
        $errorKey,
        $message = null,
        $errors = null,
        array $headers = [],
        BaseException $previous = null,
        $code = 0
    ) {
        $httpErrorCode = Response::HTTP_UNPROCESSABLE_ENTITY;

        Exception::__construct($errorKey, $message, $httpErrorCode, $errors, $headers, $previous, $code);
    }
}
