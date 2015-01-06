<?php namespace Groupeat\Support;

use Groupeat\Support\Exceptions\BadRequest;
use Groupeat\Support\Exceptions\Exception;
use Groupeat\Support\Exceptions\Forbidden;
use Groupeat\Support\Exceptions\Unauthorized;
use Groupeat\Support\Middlewares\ApiCorsHeaders;
use Groupeat\Support\Providers\WorkbenchPackageProvider;
use Response;

class PackageProvider extends WorkbenchPackageProvider {

    protected $require = [self::HELPERS];
    protected $console = ['DbInstall', 'PublishAllAssets', 'Pull'];


    public function register()
    {
        $this->app->middleware(new ApiCorsHeaders($this->app));

        parent::register();
    }

}
