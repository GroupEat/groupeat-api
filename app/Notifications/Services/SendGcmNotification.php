<?php
namespace Groupeat\Notifications\Services;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Notifications\Values\GcmApiKey;
use GuzzleHttp\Client;
use Illuminate\Foundation\Inspiring;

class SendGcmNotification
{
    const URL = 'https://android.googleapis.com/gcm/send';

    private $client;
    private $apiKey;

    public function __construct(GcmApiKey $apiKey)
    {
        $this->client = new Client;
        $this->apiKey = $apiKey;
    }

    public function call(Customer $customer)
    {
        $notificationToken = Device::where('customer_id', $customer->id)->first()->notificationToken;

        return $this->client->post(static::URL, [
            'json' => [
                'registration_ids' => [$notificationToken],
                'data' => [
                    'message' => Inspiring::quote(),
                ],
            ],
            'headers' => [
                'Authorization' => "key={$this->apiKey}",
                'Content-type' => 'application/json',
            ],
        ]);
    }
}
