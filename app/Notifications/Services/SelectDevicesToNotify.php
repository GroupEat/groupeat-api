<?php
namespace Groupeat\Notifications\Services;

use Carbon\Carbon;
use Closure;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Devices\Entities\Status;
use Groupeat\Orders\Entities\DeliveryAddress;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Values\JoinableDistanceInKms;
use Groupeat\Settings\Entities\CustomerSettings;
use Illuminate\Support\Collection;

class SelectDevicesToNotify
{
    /**
     * @var float
     */
    private $joinableDistanceInKms;

    public function __construct(JoinableDistanceInKms $joinableDistanceInKms)
    {
        $this->joinableDistanceInKms = $joinableDistanceInKms->value();
    }

    /**
     * @param GroupOrder $groupOrder
     *
     * @return \Illuminate\Support\Collection
     */
    public function call(GroupOrder $groupOrder)
    {
        $customersAroundIds = $this->getCustomersAroundIds($groupOrder->getInitiatingOrder()->deliveryAddress);
        $customersAlreadyInIds = $this->getCustomersAlreadyInIds($groupOrder);
        $potentialCustomersToNotifyIds = $customersAroundIds->diff($customersAlreadyInIds);
        $customersThatCanBeNotifiedIds = $this->getCustomersThatCanBeNotifiedIds(
            $groupOrder,
            $potentialCustomersToNotifyIds
        );

        return Device::whereIn('customerId', $customersThatCanBeNotifiedIds->all())
            ->with('customer', 'platform')
            ->get();
    }

    private function getCustomersAroundIds(DeliveryAddress $firsDeliveryAddress)
    {
        $query = Device::query()->withinKilometers(
            $firsDeliveryAddress->location,
            $this->joinableDistanceInKms
        );

        return $query->lists('customerId');
    }

    private function getCustomersAlreadyInIds(GroupOrder $groupOrder)
    {
        return $groupOrder->orders->map(function (Order $order) {
            return $order->customer->id;
        });
    }

    private function getCustomersThatCanBeNotifiedIds(GroupOrder $groupOrder, Collection $potentialCustomersToNotifyIds)
    {
        $query = CustomerSettings::query()
            ->where(CustomerSettings::NOTIFICATIONS_ENABLED, true)
            ->where(CustomerSettings::NO_NOTIFICATION_AFTER, '>', Carbon::now()->toTimeString())
            ->whereIn('customerId', $potentialCustomersToNotifyIds->all());

        return $query->lists('customerId');
    }
}
