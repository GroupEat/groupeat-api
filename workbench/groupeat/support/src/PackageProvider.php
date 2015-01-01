<?php namespace Groupeat\Support;

use Groupeat\Support\Exceptions\BadRequest;
use Groupeat\Support\Exceptions\Exception;
use Groupeat\Support\Exceptions\Forbidden;
use Groupeat\Support\Providers\WorkbenchPackageProvider;
use Response;

class PackageProvider extends WorkbenchPackageProvider {

    protected $require = ['helpers'];
    protected $console = ['DbInstall'];


    public function register()
    {
        $this->app['api.exception']->register(function(BadRequest $exception)
        {
            return $this->getResponseFrom($exception);
        });

        $this->app['api.exception']->register(function(Forbidden $exception)
        {
            return $this->getResponseFrom($exception);
        });

        parent::register();
    }

    private function getResponseFrom(Exception $exception)
    {
        $data = [
            'status_code' => $exception->getStatusCode(),
            'message' => $exception->getMessage(),
        ];

        if ($exception->hasErrors())
        {
            $data['errors'] = $exception->getErrors();
        }

        return Response::make($data, $exception->getStatusCode(), $exception->getHeaders());
    }

}
