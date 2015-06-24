<?php
namespace Groupeat\Settings\Entities;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Settings\Migrations\CustomerSettingsMigration;
use Groupeat\Support\Entities\Abstracts\Entity;
use Groupeat\Support\Exceptions\NotFound;

class CustomerSettings extends Entity
{
    const NOTIFICATIONS_ENABLED = 'notificationsEnabled';
    const DAYS_WITHOUT_NOTIFYING = 'daysWithoutNotifying';
    const NO_NOTIFICATION_AFTER = 'noNotificationAfter';

    const LABELS = [
        self::NOTIFICATIONS_ENABLED,
        self::DAYS_WITHOUT_NOTIFYING,
        self::NO_NOTIFICATION_AFTER
    ];

    public $timestamps = false;

    protected $casts = [
        self::NOTIFICATIONS_ENABLED => 'boolean',
        self::DAYS_WITHOUT_NOTIFYING => 'integer',
    ];

    protected $hidden = ['id', 'customerId', 'customer'];

    public function getRules()
    {
        return [
            'customerId' => 'required',
            static::NOTIFICATIONS_ENABLED => 'required|boolean',
            static::DAYS_WITHOUT_NOTIFYING => 'required|integer',
            static::NO_NOTIFICATION_AFTER => 'required|date_format:H:i:s'
        ];
    }

    /**
     * @param Customer $customer
     *
     * @return static
     */
    public static function findByCustomerOrFail(Customer $customer)
    {
        $setting = static::query()->where('customerId', $customer->id)->first();

        if ($setting == null) {
            throw new NotFound(
                'customerSettingNotFound',
                "Cannot find customer setting for {$customer->toShortString()}"
            );
        }

        return $setting;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    protected function getRelatedMigration()
    {
        return new CustomerSettingsMigration;
    }
}
