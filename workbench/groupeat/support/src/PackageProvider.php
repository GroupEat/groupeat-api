<?php namespace Groupeat\Support;

use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider {

    protected $require = ['helpers'];
    protected $console = ['DbInstall'];


}
