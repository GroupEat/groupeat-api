<?php
namespace Groupeat\Notifications\Services;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Notifications\Values\GcmApiKey;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use GuzzleHttp\Client;
use Illuminate\Foundation\Inspiring;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class SendGcmNotification
{
    const URL = 'https://android.googleapis.com/gcm/send';

    private $client;
    private $apiKey;
    private $logger;

    public function __construct(GcmApiKey $apiKey, LoggerInterface $logger)
    {
        $this->client = new Client;
        $this->apiKey = $apiKey;
        $this->logger = $logger;
    }

    public function call(Customer $customer)
    {
        $device = Device::where('customer_id', $customer->id)->first();

        if (!is_null($device)) {
            $notificationToken = $device->notificationToken;

            $data = [
                'message' => Inspiring::quote(),
            ];

            $response = $this->client->post(static::URL, [
                'json' => [
                    'registration_ids' => [$notificationToken],
                    'data' => $data,
                ],
                'headers' => [
                    'Authorization' => "key={$this->apiKey}",
                    'Content-type' => 'application/json',
                ],
            ]);

            if ($response->getStatusCode() == Response::HTTP_OK) {
                $this->logger->info(
                    "Notification has been sent to {$customer->toShortString()} with data: "
                    . json_encode($data).'.'
                );

                return $response;
            } else {
                throw new UnprocessableEntity(
                    'gcmError',
                    $response->getBody()
                );
            }
        } else {
            throw new UnprocessableEntity(
                'noDeviceAttached',
                "Cannot send notification to {$customer->toShortString()} without any device attached."
            );
        }
    }
}
