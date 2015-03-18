<?php
namespace Groupeat\Support\Exceptions;

use Exception as BaseException;
use Groupeat\Support\Exceptions\Exception as GroupeatException;
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
     * @param  BaseException  $e
     *
     * @return void
     */
    public function report(BaseException $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  BaseException  $e
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, BaseException $e)
    {
        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, HEAD, OPTIONS',
            'Access-Control-Allow-Headers' => 'Origin, Content-Type, Accept, Authorization, X-Request-With',
        ];

        if ($e instanceof GroupeatException) {
            $statusCode = $e->getStatusCode();
            $data['errorKey'] = $e->getErrorKey();
            $data['message'] = $e->getMessage();

            $errors = $e->getErrors();

            if (!$errors->isEmpty()) {
                $data['errors'] = $errors;
            }
        } elseif ($e instanceof HttpException && in_array($e->getStatusCode(), [404, 503])) {
            if ($e->getStatusCode() == 404) {
                return response()->json([
                    'errorKey' => 'notFound',
                    'message' => "The route ".$request->method()." ".$request->fullUrl()." does not exist.",
                ], 404, $headers);
            } elseif ($e->getStatusCode() == 503) {
                return response()->json([
                    'errorKey' => 'maintenanceMode',
                    'message' => "The application is in maintenance mode. Please come back in a few minutes.",
                ], 503, $headers);
            }
        } else {
            $statusCode = 500;
            $data['errorKey'] = 'internalServerError';
            $data['message'] = \App::isLocal() ? $e->getMessage() : "The server encountered an internal error";
        }

        if (\Config::get('app.debug')) {
            $data['debug'] = [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'class' => get_class($e),
            ];

            if (true || !\App::runningInConsole()) {
                $data['debug']['trace'] = explode("\n", $e->getTraceAsString());
            }
        }

        return response()->json(compact('data'), $statusCode, $headers);
    }
}
