<?php
namespace Groupeat\Support\Exceptions;

use App;
use Config;
use Dingo\Api\Exception\Handler as DingoExceptionHandler;
use Exception as BaseException;
use Groupeat\Support\Exceptions\Exception as GroupeatException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends DingoExceptionHandler
{
    private $logger;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    private $dontReport = [
        \Symfony\Component\HttpKernel\Exception\HttpException::class
    ];

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Report or log an exception.
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param BaseException $e
     *
     * @return void
     */
    public function report(BaseException $e)
    {
        if ($this->shouldReport($e)) {
            $this->logger->error($e);
        }
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
        $isDebug = Config::get('app.debug');
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
                    'message' => "The current route does not exist.",
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

            if (App::runningInConsole()) {
                unset($data['debug']['trace']);
            }
        }

        return response()->json(compact('data'), $statusCode);
    }

    public function handle(BaseException $exception)
    {
        return $this->render(null, $exception);
    }

    /**
     * Render an exception to the console.
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @param  BaseException                                     $e
     *
     * @return void
     */
    public function renderForConsole($output, BaseException $e)
    {
        (new ConsoleApplication)->renderException($e, $output);
    }

    private function shouldReport(BaseException $e)
    {
        foreach ($this->dontReport as $type) {
            if ($e instanceof $type) {
                return false;
            }
        }

        return true;
    }
}
