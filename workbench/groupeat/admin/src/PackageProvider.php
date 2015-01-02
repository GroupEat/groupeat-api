<?php namespace Groupeat\Admin;

use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider {

    protected $require = [self::FILTERS, self::ROUTES];

}
