<?php namespace Groupeat\Core;

use Groupeat\Core\Support\Providers\WorkbenchServiceProvider;

class ServiceProvider extends WorkbenchServiceProvider {

    protected $filesToRequire = ['helpers', 'filters'];

}
