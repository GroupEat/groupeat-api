<?php namespace Groupeat\Orders;

use Groupeat\Orders\Services\PlaceOrder;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider {

    protected $require = [self::ROUTES];

    public function register()
    {
        parent::register();

        $this->app->bind('PlaceOrderService', function($app)
        {
            return new PlaceOrder(
                $app['config']->get('orders::maximum_foodrush_in_minutes'),
                $app['config']->get('restaurants::around_distance_in_kilometers'),
                $app['config']->get('restaurants::opening_duration_in_minutes'),
                $app['config']->get('customers::address_constraints')
            );
        });
    }

}
