<?php namespace Groupeat\Admin;

use Groupeat\Admin\Entities\Admin;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider {

    protected $require = [self::ROUTES];

    public function boot()
    {
        parent::boot();

        $this->app->make('groupeat.auth')->addUserType(new Admin);
    }

}
