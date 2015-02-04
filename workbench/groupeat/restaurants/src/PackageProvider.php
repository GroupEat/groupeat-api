<?php namespace Groupeat\Restaurants;

use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Restaurants\Services\SendGroupOrderHasBeenCreatedMail;
use Groupeat\Restaurants\Services\SendGroupOrderHasBeenJoinedMail;
use Groupeat\Restaurants\Services\SendGroupOrderHasEndedMail;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider {

    protected $require = [self::ROUTES];


    public function register()
    {
        parent::register();

        $this->app->bind('SendGroupOrderHasBeenCreatedMailService', function($app)
        {
            return new SendGroupOrderHasBeenCreatedMail($app['SendMailService']);
        });

        $this->app->bind('SendGroupOrderHasBeenJoinedMailService', function($app)
        {
            return new SendGroupOrderHasBeenJoinedMail($app['SendMailService']);
        });

        $this->app->bind('SendGroupOrderHasEndedMailService', function($app)
        {
            return new SendGroupOrderHasEndedMail($app['SendMailService']);
        });
    }

    public function boot()
    {
        parent::boot();

        $this->app['groupeat.auth']->addUserType(new Restaurant);

        $this->app['events']->listen('groupOrderHasBeenCreated', 'SendGroupOrderHasBeenCreatedMailService@call');
        $this->app['events']->listen('groupOrderHasBeenJoined', 'SendGroupOrderHasBeenJoinedMailService@call');
        $this->app['events']->listen('groupOrderHasEnded', 'SendGroupOrderHasEndedMailService@call');
    }

}
