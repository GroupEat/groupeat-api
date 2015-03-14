<?php
namespace Groupeat\Admin;

use Groupeat\Admin\Entities\Admin;
use Groupeat\Auth\Auth;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $require = [self::ROUTES];

    public function boot()
    {
        parent::boot();

        $this->app[Auth::class]->addUserType(new Admin);
    }
}
