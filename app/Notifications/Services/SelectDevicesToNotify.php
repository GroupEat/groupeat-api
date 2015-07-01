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
use Groupeat\Orders\Values\AroundDistanceInKms;
use Groupeat\Settings\Entities\CustomerSettings;

class SelectDevicesToNotify
{
    /**
     * @var float
     */
    private $aroundDistanceInKms;

    public function __construct(AroundDistanceInKms $aroundDistanceInKms)
    {
        $this->aroundDistanceInKms = $aroundDistanceInKms->value();
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
        $query = Device::query()->aroundInKilometers(
            $firsDeliveryAddress->latitude,
            $firsDeliveryAddress->longitude,
            $this->aroundDistanceInKms
        );

        return $query->lists('customerIds');
    }

    private function getCustomersAlreadyInIds(GroupOrder $groupOrder)
    {
        return $groupOrder->orders->map(function (Order $order) {
            return $order->customer->id;
        });
    }

    private function getCustomersThatCanBeNotifiedIds(GroupOrder $groupOrder, array $potentialCustomersToNotifyIds)
    {
        $query = CustomerSettings::query()
            ->where(CustomerSettings::NOTIFICATIONS_ENABLED, true)
            ->where(CustomerSettings::NO_NOTIFICATION_AFTER, '>', Carbon::now()->toTimeString())
            ->whereIn('customerId', $potentialCustomersToNotifyIds);

        return $query->lists('customerId');
    }
}
