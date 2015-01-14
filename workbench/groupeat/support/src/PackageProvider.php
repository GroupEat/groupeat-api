<?php namespace Groupeat\Support;

use Groupeat\Support\Exceptions\BadRequest;
use Groupeat\Support\Exceptions\Exception;
use Groupeat\Support\Exceptions\Forbidden;
use Groupeat\Support\Exceptions\Unauthorized;
use Groupeat\Support\Providers\WorkbenchPackageProvider;
use Response;

class PackageProvider extends WorkbenchPackageProvider {

    protected $require = [self::FILTERS, self::HELPERS, self::ROUTES];
    protected $console = ['DbInstall'];

}
