<?php
namespace Groupeat\Notifications\Services;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Notifications\Entities\Device;
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
        $registrationId = Device::where('customer_id', $customer->id)->first()->device_id;

        return $this->client->post(static::URL, [
            'json' => [
                'registration_ids' => [$registrationId],
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
