<?php
namespace Groupeat\Support\Exceptions;

use Exception as BaseException;
use Symfony\Component\HttpFoundation\Response;

class BadRequest extends Exception
{
    public function __construct(
        $errorKey,
        $message = null,
        $errors = null,
        array $headers = [],
        BaseException $previous = null,
        $code = 0
    ) {
        Exception::__construct($errorKey, $message, Response::HTTP_BAD_REQUEST, $errors, $headers, $previous, $code);
    }
}
