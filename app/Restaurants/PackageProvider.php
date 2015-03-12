<?php namespace Groupeat\Restaurants;

use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Restaurants\Services\SendGroupOrderHasEndedMail;
use Groupeat\Restaurants\Services\SendOrderHasBeenPlacedMail;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $require = [self::ROUTES];


    public function register()
    {
        parent::register();

        $this->app->bind('SendOrderHasBeenPlacedMailService', function ($app) {
            return new SendOrderHasBeenPlacedMail($app['SendMailService']);
        });

        $this->app->bind('SendGroupOrderHasEndedMailService', function ($app) {
            $tokenTtlInMinutes = 2 * $app['config']->get('orders.maximum_preparation_time_in_minutes');

            return new SendGroupOrderHasEndedMail(
                $app['SendMailService'],
                $app['url'],
                $app['GenerateAuthTokenService'],
                $tokenTtlInMinutes
            );
        });
    }

    public function boot()
    {
        parent::boot();

        $this->app['groupeat.auth']->addUserType(new Restaurant);

        $this->app['events']->listen('groupOrderHasBeenCreated', 'SendOrderHasBeenPlacedMailService@created');
        $this->app['events']->listen('groupOrderHasBeenJoined', 'SendOrderHasBeenPlacedMailService@joined');
        $this->app['events']->listen('groupOrderHasEnded', 'SendGroupOrderHasEndedMailService@call');
    }
}
