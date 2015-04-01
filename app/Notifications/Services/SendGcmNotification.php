<?php
namespace Groupeat\Notifications\Services;

use Groupeat\Notifications\Entities\Notification;
use Groupeat\Notifications\Values\GcmApiKey;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
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

    public function call(Notification $notification)
    {
        $customer = $notification->customer;
        $device = $notification->device;
        $groupOrder = $notification->groupOrder;

        $notificationToken = $device->notificationToken;

        $data = [
            'message' => "A new group order has been created. Join it now!",
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
                "Notification has been sent to {$customer->toShortString()} with data: "
                . json_encode($data).'.'
            );

            return $response;
        } else {
            throw new UnprocessableEntity(
                'gcmError',
                (string) $response->getBody()
            );
        }
    }
}
