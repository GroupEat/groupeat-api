<?php
namespace Groupeat\Notifications\Services;

use Carbon\Carbon;
use DB;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Notifications\Support\ExecuteWhileNotEmptyChain;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Values\JoinableDistanceInKms;
use Groupeat\Settings\Entities\CustomerSettings;
use Illuminate\Support\Collection;
use Psr\Log\LoggerInterface;

class SelectDevicesToNotify
{
    private $logger;
    private $joinableDistanceInKms;

    public function __construct(LoggerInterface $logger, JoinableDistanceInKms $joinableDistanceInKms)
    {
        $this->logger = $logger;
        $this->joinableDistanceInKms = $joinableDistanceInKms->value();
    }

    public function call(GroupOrder $groupOrder): Collection
    {
        $chain = new ExecuteWhileNotEmptyChain($this->logger, class_basename($this), ['groupOrderId' => $groupOrder->id]);

        return collect($chain
            ->next(function () use ($groupOrder) {
                return Customer::lists('id');
            }, 'allCustomersIds')
            ->next(function (Collection $customersAroundIds) use ($groupOrder) {
                $customersAlreadyInIds = $groupOrder->orders->map(function (Order $order) {
                    return $order->customerId;
                });

                return $customersAroundIds->diff($customersAlreadyInIds)->values();
            }, 'customersNotAlreadyInIds')
            ->next(function (Collection $customersAroundNotAlreadyInIds) {
                return CustomerSettings::whereIn('customerId', $customersAroundNotAlreadyInIds->all())
                    ->where(CustomerSettings::NOTIFICATIONS_ENABLED, true)
                    ->where(CustomerSettings::NO_NOTIFICATION_AFTER, '>', Carbon::now()->toTimeString())
                    ->lists('customerId');
            }, 'customersThatCanBeNotifiedAtThisTimeIds')
            ->next(function (Collection $customersThatCanBeNotifiedAtThisTimeIds) use ($chain, $groupOrder) {
                $customerIdToDaysWithoutNotifying = CustomerSettings::whereIn(
                    'customerId',
                    $customersThatCanBeNotifiedAtThisTimeIds->all()
                )->lists('daysWithoutNotifying', 'customerId')->all();

                $orderEntity = new Order;
                $customersLastOrderDatesSql = 'SELECT DISTINCT ON ('.$orderEntity->getRawTableField('customerId').') '
                    .$orderEntity->getRawTableField('customerId')
                    .', '.$orderEntity->getRawTableField(Order::CREATED_AT)
                    .' FROM '.$orderEntity->getTable()
                    .' WHERE '.$orderEntity->getRawTableField('customerId').' IN ('
                    .implode(',', $customersThatCanBeNotifiedAtThisTimeIds->all())
                    .')'
                    .' ORDER BY '.$orderEntity->getRawTableField('customerId')
                    .', '.$orderEntity->getRawTableField(Order::CREATED_AT).' DESC';
                $customersLastOrderDates = collect(DB::select(DB::raw($customersLastOrderDatesSql)));
                $chain->log('customersLastOrderDates', $customersLastOrderDates);

                $customersThatOrderedTooRecentlyIds = $customersLastOrderDates
                    ->filter(function ($record) use ($customerIdToDaysWithoutNotifying) {
                        $lastOrderDate = new Carbon($record->createdAt);
                        $daysWithoutNotifying = $customerIdToDaysWithoutNotifying[$record->customerId];

                        return $lastOrderDate->diffInDays() < $daysWithoutNotifying;
                    })
                    ->lists('customerId');
                $chain->log('customersThatOrderedTooRecentlyIds', $customersThatOrderedTooRecentlyIds);

                return $customersThatCanBeNotifiedAtThisTimeIds->diff($customersThatOrderedTooRecentlyIds);
            }, 'customersThatCanBeNotifiedIds')
            ->next(function (Collection $customersThatCanBeNotifiedIds) use ($chain) {
                $devices = Device::whereIn('customerId', $customersThatCanBeNotifiedIds->all())
                    ->whereNotNull('notificationToken')
                    ->with('customer', 'platform', 'locations')
                    ->get();
                $chain->log('devicesToNotifyIds', $devices->lists('id'));

                return $devices;
            })
            ->get());
    }
}
