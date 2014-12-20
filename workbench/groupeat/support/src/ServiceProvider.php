<?php namespace Groupeat\Support;

use Groupeat\Support\Providers\WorkbenchServiceProvider;

class ServiceProvider extends WorkbenchServiceProvider {

    protected $filesToRequire = ['helpers', 'filters'];

}
