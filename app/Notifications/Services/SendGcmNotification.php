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
    const LED_COLOR_ARGB = [0, 255, 78, 80];

    private $client;
    private $key;

    public function __construct(GcmKey $key)
    {
        $this->client = new Client;
        $this->key = $key->value();
    }

    public function call(Notification $notification): string
    {
        $data = [];

        if (!$notification->isSilent()) {
            $data['ledColor'] = self::LED_COLOR_ARGB;
            $data['title'] = $notification->getTitle();
            $data['message'] = $notification->getMessage();
        }

        foreach ($notification->getAdditionalData() as $key => $value) {
            $data[$key] = $value;
        }

        $payload = [
            'json' => [
                'to' => $notification->getDevice()->notificationToken,
                'data' => $data,
            ],
            'headers' => [
                'Authorization' => "key={$this->key}",
                'Content-type' => 'application/json',
            ],
        ];

        if ($notification->getTimeToLiveInSeconds()) {
            $payload['json']['time_to_live'] = $notification->getTimeToLiveInSeconds();
        }

        try {
            $response = $this->client->post(static::URL, $payload);
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
