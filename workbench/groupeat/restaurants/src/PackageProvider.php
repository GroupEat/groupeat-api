<?php namespace Groupeat\Restaurants;

use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Restaurants\Services\SendGroupOrderHasBeenCreatedMail;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider {

    protected $require = [self::ROUTES];


    public function register()
    {
        parent::register();

        $this->app->bind('SendGroupOrderHasBeenCreatedMailService', function($app)
        {
            return new SendGroupOrderHasBeenCreatedMail(
                $app['mailer'],
                $app['groupeat.locale']
            );
        });
    }

    public function boot()
    {
        parent::boot();

        $this->app['groupeat.auth']->addUserType(new Restaurant);

        $this->app['events']->listen('groupOrderHasBeenCreated', 'SendGroupOrderHasBeenCreatedMailService@call');
    }

}
