<?php namespace Groupeat\App;

use Groupeat\Support\Providers\WorkbenchServiceProvider;

class ServiceProvider extends WorkbenchServiceProvider {

    protected $filesToRequire = ['routes', 'filters'];

}
