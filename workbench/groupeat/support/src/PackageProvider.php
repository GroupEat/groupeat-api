<?php namespace Groupeat\Support;

use Groupeat\Support\Providers\WorkbenchPackageProvider;
use Groupeat\Support\Services\Errors;

class PackageProvider extends WorkbenchPackageProvider {

    protected $require = ['helpers'];
    protected $console = ['DbInstall'];

}
