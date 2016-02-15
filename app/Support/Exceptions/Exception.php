<?php
namespace Groupeat\Support\Exceptions;

use Exception as BaseException;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Exception extends HttpException
{
    protected $errorKey;

    public function __construct(
        $errorKey,
        $message = null,
        $statusCode = 500,
        $errors = null,
        array $headers = [],
        BaseException $previous = null,
        $code = 0
    ) {
        if (is_array($errorKey)) {
            $errors = $this->formatErrors($errorKey);
            $errorKey = 'validationErrors';
        }

        $this->errorKey = $errorKey;

        if (is_null($errors)) {
            $this->errors = new MessageBag;
        } else {
            $this->errors = is_array($errors) ? new MessageBag($errors) : $errors;
        }

        HttpException::__construct($statusCode, $message, $previous, $headers, $code);
    }

    public function getErrorKey()
    {
        return $this->errorKey;
    }

    public function getErrors(): MessageBag
    {
        return $this->errors;
    }

    protected function formatErrors(array $errors): array
    {
        $formattedErrors = [];

        foreach ($errors as $attribute => $rules) {
            foreach ($rules as $rule => $details) {
                $rule = lcfirst($rule);

                switch ($rule) {
                    case 'unique':
                        $rule = 'alreadyTaken';
                        break;
                }

                $formattedErrors[$attribute][$rule] = $details;
            }
        }

        return $formattedErrors;
    }
}
