<?php namespace Groupeat\Support\Exceptions;

use Exception as BaseException;
use Dingo\Api\Event\ExceptionHandler as DingoExceptionHandler;

class ExceptionHandler extends DingoExceptionHandler {

    /**
     * Handle an exception thrown during dispatching of an API request.
     *
     * @param BaseException $exception
     *
     * @throws BaseException
     *
     * @return \Dingo\Api\Http\Response
     */
    public function handle(BaseException $exception)
    {
        $response = parent::handle($exception);

        if ($exception instanceof Exception)
        {
            $data = json_decode($response->getContent(), true);

            if (!is_null($data))
            {
                $data['error_key'] = $exception->getErrorKey();
            }

            $response->setContent($data);
        }

        return $response;
    }

}
