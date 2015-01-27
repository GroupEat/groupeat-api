<?php namespace Groupeat\Orders;

use Groupeat\Orders\Services\CreateGroupOrder;
use Groupeat\Orders\Services\JoinGroupOrder;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider {

    protected $require = [self::ROUTES];

    public function register()
    {
        parent::register();

        $this->app->bind('CreateGroupOrderService', function($app)
        {
            return new CreateGroupOrder(
                $app['config']->get('restaurants::around_distance_in_kilometers'),
                $app['config']->get('customers::address_constraints'),
                $app['config']->get('orders::maximum_foodrush_in_minutes'),
                $app['config']->get('restaurants::opening_duration_in_minutes')
            );
        });

        $this->app->bind('JoinGroupOrderService', function($app)
        {
            return new JoinGroupOrder(
                $app['config']->get('restaurants::around_distance_in_kilometers'),
                $app['config']->get('customers::address_constraints')
            );
        });
    }

}
