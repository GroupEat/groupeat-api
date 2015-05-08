<?php
namespace Groupeat\Notifications\Services;

use Groupeat\Notifications\Entities\Notification;
use Groupeat\Notifications\Services\Abstracts\NotificationSender;
use Groupeat\Notifications\Values\GcmApiKey;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Groupeat\Support\Services\Locale;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class SendGcmNotification extends NotificationSender
{
    const URL = 'https://android.googleapis.com/gcm/send';

    private $client;
    private $apiKey;

    public function __construct(Locale $locale, LoggerInterface $logger, GcmApiKey $apiKey)
    {
        parent::__construct($locale, $logger);

        $this->client = new Client;
        $this->apiKey = $apiKey;
    }

    public function call(Notification $notification)
    {
        $customer = $notification->customer;
        $device = $notification->device;
        $groupOrder = $notification->groupOrder;

        $notificationToken = $device->notificationToken;

        $data = [
            'message' => $this->translateFor('joinGroupOrder', $customer->credentials),
            'groupOrderId' => $groupOrder->id,
        ];

        try {
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
        } catch (ClientException $e) {
            throw new UnprocessableEntity(
                'gcmError',
                (string) $e->getResponse()->getBody()
            );
        }

        if ($response->getStatusCode() == Response::HTTP_OK) {
            $notification->createdAt = $notification->freshTimestamp();
            $notification->save();

            $this->logger->info(
                "GCM notification has been sent to {$customer->toShortString()} with data: "
                . json_encode($data).'.'
            );
        } else {
            throw new UnprocessableEntity(
                'gcmError',
                (string) $response->getBody()
            );
        }
    }
}
