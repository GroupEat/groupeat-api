<?php
namespace Groupeat\Notifications\Services;

use Groupeat\Notifications\Values\GcmKey;
use Groupeat\Notifications\Values\Notification;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Groupeat\Support\Services\Locale;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;

class SendGcmNotification
{
    const URL = 'https://android.googleapis.com/gcm/send';

    private $client;
    private $key;

    public function __construct(GcmKey $key)
    {
        $this->client = new Client;
        $this->key = $key->value();
    }

    public function call(Notification $notification)
    {
        $data = [
            'title' => $notification->getTitle(),
            'message' => $notification->getMessage(),
        ];

        foreach ($notification->getAdditionalData() as $key => $value) {
            $data[$key] = $value;
        }

        try {
            $response = $this->client->post(static::URL, [
                'json' => [
                    'to' => $notification->getDevice()->notificationToken,
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

        return (string) $response->getBody();
    }
}
