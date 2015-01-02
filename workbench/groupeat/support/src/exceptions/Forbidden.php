<?php namespace Groupeat\Support\Exceptions;

use Exception as BaseException;
use Symfony\Component\HttpFoundation\Response;

class Forbidden extends Exception {

    public function __construct(
        $message = null,
        $errors = null,
        array $headers = [],
        BaseException $previous = null,
        $code = 0
    )
    {
        parent::__construct($message, Response::HTTP_FORBIDDEN, $errors, $headers, $previous, $code);
    }

}