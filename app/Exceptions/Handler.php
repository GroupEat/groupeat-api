<?php
namespace Groupeat\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        'Symfony\Component\HttpKernel\Exception\HttpException'
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     *
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, HEAD, OPTIONS',
            'Access-Control-Allow-Headers' => 'Origin, Content-Type, Accept, Authorization, X-Request-With',
        ];

        if ($e instanceof \Groupeat\Support\Exceptions\Exception) {
            $statusCode = $e->getStatusCode();
            $data['errorKey'] = $e->getErrorKey();
            $data['message'] = $e->getMessage();
            $data['errors'] = $e->getErrors();
        } elseif ($e instanceof HttpException && in_array($e->getStatusCode(), [404, 503])) {
            if ($e->getStatusCode() == 404) {
                return \Response::json([
                    'errorKey' => 'notFound',
                    'message' => "The route ".$request->method()." ".$request->fullUrl()." does not exist.",
                ], 404, $headers);
            } elseif ($e->getStatusCode() == 503) {
                return \Response::json([
                    'errorKey' => 'maintenanceMode',
                    'message' => "The application is in maintenance mode. Please come back in a few minutes.",
                ], 503, $headers);
            }
        } else {
            $statusCode = 500;
            $data['errorKey'] = 'internalServerError';
            $data['message'] = \App::isLocal() ? $e->getMessage() : "The server encountered an internal error";
        }

        if (\App::isLocal()) {
            $data['debug'] = [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'class' => get_class($e),
            ];

            if (true || !\App::runningInConsole()) {
                $data['debug']['trace'] = explode("\n", $e->getTraceAsString());
            }
        }

        return \Response::json(compact('data'), $statusCode, $headers);
    }
}
