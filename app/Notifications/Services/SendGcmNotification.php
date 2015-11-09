<?php
namespace Groupeat\Notifications\Services;

use Groupeat\Notifications\Entities\Notification;
use Groupeat\Notifications\Services\Abstracts\NotificationSender;
use Groupeat\Notifications\Values\GcmKey;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Groupeat\Support\Services\Locale;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;

class SendGcmNotification extends NotificationSender
{
    const URL = 'https://android.googleapis.com/gcm/send';

    private $client;
    private $key;

    public function __construct(Locale $locale, GcmKey $key)
    {
        parent::__construct($locale);

        $this->client = new Client;
        $this->key = $key->value();
    }

    public function call(Notification $notification)
    {
        $customer = $notification->customer;
        $device = $notification->device;
        $groupOrder = $notification->groupOrder;
        $maximumDiscountRate = $groupOrder->restaurant->maximumDiscountRate;


        $notificationToken = $device->notificationToken;

        $data = [
            'title' => $this->translateFor('title', $customer->credentials),
            'message' => $this->translateFor('message', $customer->credentials, compact('maximumDiscountRate')),
            'groupOrderId' => $groupOrder->id,
        ];

        try {
            $response = $this->client->post(static::URL, [
                'json' => [
                    'to' => $notificationToken,
                    'data' => $data,
                    'time_to_live' => $notification->getTimeToLiveInSeconds(),
                ],
                'headers' => [
                    'Authorization' => "key={$this->key}",
                    'Content-type' => 'application/json',
                ],
            ]);
        } catch (ClientException $e) {
            throw new UnprocessableEntity(
                'gcmError',
                (string) $e->getResponse()->getBody()
            );
        }

        if ($response->getStatusCode() != Response::HTTP_OK) {
            throw new UnprocessableEntity(
                'gcmError',
                (string) $response->getBody()
            );
        }
    }
}
