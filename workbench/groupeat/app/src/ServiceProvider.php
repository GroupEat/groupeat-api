<?php namespace Groupeat\App;

use Groupeat\Core\Support\Providers\WorkbenchServiceProvider;

class ServiceProvider extends WorkbenchServiceProvider {

    protected $filesToRequire = ['routes', 'filters'];

}
