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
        $isDebug = \Config::get('app.debug');
        $debugInfo = [
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'class' => get_class($e),
            'trace' => explode("\n", $e->getTraceAsString()),
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
                $json = [
                    'errorKey' => 'notFound',
                    'message' => "The route ".$request->method()." ".$request->fullUrl()." does not exist.",
                ];

                if ($isDebug) {
                    $json['debug'] = $debugInfo;
                }

                return response()->json($json, 404);
            } elseif ($e->getStatusCode() == 503) {
                $json = [
                    'errorKey' => 'maintenanceMode',
                    'message' => "The application is in maintenance mode. Please come back in a few minutes.",
                ];

                if ($isDebug) {
                    $json['debug'] = $debugInfo;
                }

                return response()->json($json, 503);
            }
        } else {
            $statusCode = 500;
            $data['errorKey'] = 'internalServerError';
            $data['message'] = $isDebug ? $e->getMessage() : "The server encountered an internal error";
        }

        if ($isDebug) {
            $data['debug'] = $debugInfo;

            if (\App::runningInConsole()) {
                unset($data['debug']['trace']);
            }
        }

        return response()->json(compact('data'), $statusCode);
    }
}
