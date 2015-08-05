<?php
namespace Groupeat\Messaging\Services;

use Groupeat\Messaging\Events\SmsHasBeenSent;
use Groupeat\Messaging\Values\NexmoKey;
use Groupeat\Messaging\Values\NexmoSecret;
use Groupeat\Messaging\Values\Sms;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Groupeat\Support\Values\Environment;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Events\Dispatcher;
use Symfony\Component\HttpFoundation\Response;

class SendSms
{
    const URL = 'https://rest.nexmo.com/sms/json';
    const FROM = 'GroupEat';

    private $environment;
    private $events;
    private $key;
    private $secret;
    private $client;

    public function __construct(
        Environment $environment,
        Dispatcher $events,
        NexmoKey $key,
        NexmoSecret $secret
    ) {
        $this->environment = $environment;
        $this->events = $events;
        $this->key = $key->value();
        $this->secret = $secret->value();

        $this->client = new Client;
    }

    public function call(Sms $sms, $force = false)
    {
        if ($force || $this->needToSend()) {
            $json = [
                'api_key' => 'wrong',
                'api_secret' => $this->secret,
                'from' => static::FROM,
                'to' => $sms->getPhoneNumber(),
                'text' => $sms->getText(),
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

    private function needToSend()
    {
        return !$this->environment->isLocal();
    }
}
