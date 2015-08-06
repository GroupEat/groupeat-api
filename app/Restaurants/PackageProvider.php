<?php namespace Groupeat\Restaurants;

use Groupeat\Auth\Auth;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Orders\Events\GroupOrderHasBeenJoined;
use Groupeat\Orders\Events\GroupOrderHasBeenClosed;
use Groupeat\Orders\Values\MaximumPreparationTimeInMinutes;
use Groupeat\Orders\Values\MinimumFoodrushInMinutes;
use Groupeat\Restaurants\Entities\Product;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Restaurants\Listeners\SendGroupOrderHasBeenClosedMail;
use Groupeat\Restaurants\Listeners\SendGroupOrderHasBeenCreatedSms;
use Groupeat\Restaurants\Listeners\SendOrderHasBeenPlacedMail;
use Groupeat\Restaurants\Values\ConfirmationTokenDurationInMinutes;
use Groupeat\Restaurants\Values\DefaultOpeningDurationInMinutes;
use Groupeat\Restaurants\Values\MaximumDeliveryDistanceInKms;
use Groupeat\Restaurants\Values\MinimumOpeningDurationInMinutes;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;
use Illuminate\Config\Repository;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $configValues = [
        MaximumDeliveryDistanceInKms::class => 'restaurants.around_distance_in_kilometers',
    ];

    protected $routeEntities = [
        Restaurant::class => 'restaurant',
        Product::class => 'product',
    ];

    protected $listeners = [
        GroupOrderHasBeenCreated::class => SendOrderHasBeenPlacedMail::class.'@created',
        GroupOrderHasBeenCreated::class => SendGroupOrderHasBeenCreatedSms::class,
        GroupOrderHasBeenJoined::class => SendOrderHasBeenPlacedMail::class.'@joined',
        GroupOrderHasBeenClosed::class => SendGroupOrderHasBeenClosedMail::class,
    ];

    protected function registerPackage()
    {
        $this->bindValue(
            ConfirmationTokenDurationInMinutes::class,
            2 * $this->app['config']->get('orders.maximum_preparation_time_in_minutes')
        );
    }

    protected function bootPackage()
    {
        $this->app[Auth::class]->addUserType(new Restaurant);
    }
}
