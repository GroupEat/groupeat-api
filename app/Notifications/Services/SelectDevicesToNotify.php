<?php
namespace Groupeat\Notifications\Services;

use Carbon\Carbon;
use Closure;
use DB;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Devices\Entities\Status;
use Groupeat\Orders\Entities\DeliveryAddress;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Values\JoinableDistanceInKms;
use Groupeat\Settings\Entities\CustomerSettings;
use Illuminate\Support\Collection;
use Psr\Log\LoggerInterface;

class SelectDevicesToNotify
{
    private $logger;

    /**
     * @var float
     */
    private $joinableDistanceInKms;

    public function __construct(LoggerInterface $logger, JoinableDistanceInKms $joinableDistanceInKms)
    {
        $this->logger = $logger;
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
        $this->log($groupOrder, compact('customersAroundIds'));
        if ($customersAroundIds->isEmpty()) {
            return collect([]);
        }

        $customersAlreadyInIds = $this->getCustomersAlreadyInIds($groupOrder);
        $this->log($groupOrder, compact('customersAlreadyInIds'));

        $potentialCustomersToNotifyIds = $customersAroundIds->diff($customersAlreadyInIds)->values();
        $this->log($groupOrder, compact('potentialCustomersToNotifyIds'));
        if ($potentialCustomersToNotifyIds->isEmpty()) {
            return collect([]);
        }

        $customersThatCanBeNotifiedIds = $this->getCustomersThatCanBeNotifiedIds(
            $groupOrder,
            $potentialCustomersToNotifyIds
        );
        $this->log($groupOrder, compact('customersThatCanBeNotifiedIds'));
        if ($customersThatCanBeNotifiedIds->isEmpty()) {
            return collect([]);
        }

        $devices = Device::whereIn('customerId', $customersThatCanBeNotifiedIds->all())
            ->with('customer', 'platform')
            ->get();
        $this->log($groupOrder, ['devicesToNotifyIds' => $devices->lists('id')]);

        return $devices;
    }

    private function getCustomersAroundIds(DeliveryAddress $firsDeliveryAddress)
    {
        // TODO: use geolocation to return customers really around
        return Customer::lists('id');
    }

    private function getCustomersAlreadyInIds(GroupOrder $groupOrder)
    {
        return collect($groupOrder->orders->map(function (Order $order) {
            return $order->customerId;
        }));
    }

    private function getCustomersThatCanBeNotifiedIds(GroupOrder $groupOrder, Collection $potentialCustomersToNotifyIds)
    {
        $customersThatCanBeNotifiedAtThisTimeSettings =
            CustomerSettings::whereIn('customerId', $potentialCustomersToNotifyIds->all())
            ->where(CustomerSettings::NOTIFICATIONS_ENABLED, true)
            ->where(CustomerSettings::NO_NOTIFICATION_AFTER, '>', Carbon::now()->toTimeString())
            ->get();

        $customersThatCanBeNotifiedAtThisTimeIds = collect(
            $customersThatCanBeNotifiedAtThisTimeSettings->lists('customerId')
        );
        $this->log($groupOrder, compact('customersThatCanBeNotifiedAtThisTimeIds'));
        if ($customersThatCanBeNotifiedAtThisTimeIds->isEmpty()) {
            return collect([]);
        }

        $customerIdToDaysWithoutNotifying = [];
        $customersThatCanBeNotifiedAtThisTimeSettings
            ->each(function (CustomerSettings $settings) use (&$customerIdToDaysWithoutNotifying) {
                $customerIdToDaysWithoutNotifying[$settings->customerId] = $settings->daysWithoutNotifying;
            });
        $this->log($groupOrder, compact('customerIdToDaysWithoutNotifying'));

        $orderEntity = new Order;
        $customerSettingEntity = new CustomerSettings;
        $ordersTable = $orderEntity->getTable();
        $customerSettingsTable = $customerSettingEntity->getTable();

        $customersLastOrderDatesSql = 'SELECT DISTINCT ON ('.$orderEntity->getRawTableField('customerId').') '
            .$orderEntity->getRawTableField('customerId')
            .', '.$orderEntity->getRawTableField(Order::CREATED_AT)
            .' FROM '.$ordersTable
            .' WHERE '.$orderEntity->getRawTableField('customerId').' IN ('
                .implode(',', $customersThatCanBeNotifiedAtThisTimeIds->all())
            .')'
            .' ORDER BY '.$orderEntity->getRawTableField('customerId')
                .', '.$orderEntity->getRawTableField(Order::CREATED_AT).' DESC';
        $customersLastOrderDates = collect(DB::select(DB::raw($customersLastOrderDatesSql)));
        $this->log($groupOrder, compact('customersLastOrderDates'));

        $customersThatOrderedTooRecentlyIds = $customersLastOrderDates
            ->filter(function ($record) use ($customerIdToDaysWithoutNotifying) {
                $lastOrderDate = new Carbon($record->createdAt);
                $daysWithoutNotifying = $customerIdToDaysWithoutNotifying[$record->customerId];

                return $lastOrderDate->diffInDays(Carbon::now(), true) < $daysWithoutNotifying;
            })
            ->lists('customerId');
        $this->log($groupOrder, compact('customersThatOrderedTooRecentlyIds'));

        return $customersThatCanBeNotifiedAtThisTimeIds->diff($customersThatOrderedTooRecentlyIds);
    }

    private function log(GroupOrder $groupOrder, $data)
    {
        $this->logger->debug('SelectDevicesToNotify: ' . json_encode($data), ['groupOrderId' => $groupOrder->id]);
    }
}
