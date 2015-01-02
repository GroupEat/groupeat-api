<?php namespace Groupeat\Support\Exceptions;

use Exception as BaseException;
use Symfony\Component\HttpFoundation\Response;

class Unauthorized extends Exception {

    public function __construct(
        $message = null,
        $errors = null,
        array $headers = [],
        BaseException $previous = null,
        $code = 0
    )
    {
        parent::__construct($message, Response::HTTP_UNAUTHORIZED, $errors, $headers, $previous, $code);
    }

}