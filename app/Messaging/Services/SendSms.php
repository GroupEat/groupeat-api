<?php
namespace Groupeat\Messaging\Services;

use Groupeat\Messaging\Events\SmsHasBeenSent;
use Groupeat\Messaging\Values\MessagingEnabled;
use Groupeat\Messaging\Values\NexmoCredentials;
use Groupeat\Messaging\Values\Sms;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Events\Dispatcher;
use Symfony\Component\HttpFoundation\Response;

class SendSms
{
    const URL = 'https://rest.nexmo.com/sms/json';
    const FROM = 'GroupEat';

    private $events;
    private $enabled;
    private $credentials;
    private $client;

    public function __construct(Dispatcher $events, MessagingEnabled $enabled, NexmoCredentials $credentials)
    {
        $this->events = $events;
        $this->enabled = $enabled->value();
        $this->credentials = $credentials;

        $this->client = new Client;
    }

    public function call(Sms $sms, $force = false)
    {
        if ($this->enabled || $force) {
            $text = static::FROM . "\n" . $sms->getText();

            $json = [
                'api_key' => $this->credentials->getKey(),
                'api_secret' => $this->credentials->getSecret(),
                'from' => static::FROM,
                'to' => $sms->getPhoneNumber(),
                'text' => $text,
            ];

            $headers = [
                'Content-type' => 'application/json',
            ];

            try {
                $response = $this->client->post(static::URL, compact('json', 'headers'));
            } catch (ClientException $e) {
                throw new UnprocessableEntity(
                    'nexmoError',
                    (string) $e->getResponse()->getBody()
                );
            }

            $bodyData = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() == Response::HTTP_OK
                && empty($bodyData['messages'][0]['error-text'])) {
                $this->events->fire(new SmsHasBeenSent($sms));
            } else {
                throw new UnprocessableEntity(
                    'nexmoError',
                    $bodyData['messages'][0]['error-text']
                );
            }
        } else {
            $this->events->fire(new SmsHasBeenSent($sms));
        }
    }
}
