<?php namespace Groupeat\Deploy;

use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider {

    protected $console = ['OpCacheReset', 'Push'];

}
