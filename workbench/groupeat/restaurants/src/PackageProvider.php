<?php namespace Groupeat\Restaurants;

use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider {

    protected $require = [self::ROUTES];


    public function boot()
    {
        parent::boot();

        $this->app->make('groupeat.auth')->addUserType(new Restaurant);
    }

}
