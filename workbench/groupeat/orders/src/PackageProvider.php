<?php namespace Groupeat\Orders;

use Groupeat\Orders\Services\ConfirmGroupOrder;
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
                $app['events'],
                $app['config']->get('restaurants::around_distance_in_kilometers'),
                $app['config']->get('customers::address_constraints'),
                $app['config']->get('orders::minimum_foodrush_in_minutes'),
                $app['config']->get('orders::maximum_foodrush_in_minutes'),
                $app['config']->get('restaurants::opening_duration_in_minutes')
            );
        });

        $this->app->bind('JoinGroupOrderService', function($app)
        {
            return new JoinGroupOrder(
                $app['events'],
                $app['config']->get('orders::around_distance_in_kilometers'),
                $app['config']->get('customers::address_constraints')
            );
        });

        $this->app->bind('ConfirmGroupOrderService', function($app)
        {
            return new ConfirmGroupOrder(
                $app['events'],
                $app['config']->get('orders::maximum_preparation_time_in_minutes')
            );
        });
    }

}
