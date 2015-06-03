<?php
namespace Groupeat\Notifications\Services;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Settings\Entities\Setting;

class SelectDevicesToNotify
{
    const NOTIFICATIONS_ENABLED = 'notificationsEnabled';
    const DAYS_WITHOUT_NOTIFYING = 'daysWithoutNotifying';
    const NO_NOTIFICATION_AFTER = 'noNotificationAfter';

    /**
     * @param GroupOrder $groupOrder
     *
     * @return \Illuminate\Support\Collection
     */
    public function call(GroupOrder $groupOrder)
    {
        $customerAlreadyInIds = $this->getCustomerAlreadyInIds($groupOrder);
        $concernedCustomerIds = $this->getConcernedCustomerIds($groupOrder);
        $customersToNotifyIds = array_diff($customerAlreadyInIds, $concernedCustomerIds);

        return Device::whereIn('customerId', $customersToNotifyIds)
            ->with('customer', 'platform')
            ->get();
    }

    private function getCustomerAlreadyInIds(GroupOrder $groupOrder)
    {
        return $groupOrder->orders->map(function (Order $order) {
            return $order->customer->id;
        })->all();
    }

    private function getConcernedCustomerIds(GroupOrder $groupOrder)
    {
        $labels = [
            static::NOTIFICATIONS_ENABLED,
            static::DAYS_WITHOUT_NOTIFYING,
            static::NO_NOTIFICATION_AFTER
        ];

        $defaultSettings = Setting::whereIn('label', $labels)->get();

        $notificationsEnabledSetting = $defaultSettings[array_search(static::NOTIFICATIONS_ENABLED, $labels)];
        $notificationsEnabledByDefault = $notificationsEnabledSetting->default;

        return [];
    }
}
