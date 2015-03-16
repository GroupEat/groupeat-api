<?php
namespace Groupeat\Admin;

use Groupeat\Admin\Entities\Admin;
use Groupeat\Auth\Auth;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected function bootPackage()
    {
        $this->app[Auth::class]->addUserType(new Admin);
    }
}
