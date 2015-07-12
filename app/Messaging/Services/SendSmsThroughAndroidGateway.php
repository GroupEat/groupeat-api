<?php
namespace Groupeat\Messaging\Services;

use Groupeat\Support\Exceptions\UnprocessableEntity;
use GuzzleHttp\Client;

class SendSmsThroughAndroidGateway
{
    const URL = 'https://smsgateway.me/api/v3';

    private $email;
    private $password;
    private $device;

    private $client;

    public function __construct($email, $password, $device)
    {
        $this->email = $email;
        $this->password = $password;
        $this->device = $device;

        $this->client = new Client;
    }

    public function call($phoneNumber, $message)
    {
        $queryString = http_build_query([
            'email' => $this->email,
            'password' => $this->password,
            'device' => $this->device,
            'number' => $phoneNumber,
            'message' => $message,
        ]);

        $url = static::URL.'/messages/send?'.$queryString;

        $response = $this->client->post($url, ['verify' => false]);
        $body = (string) $response->getBody();
        $bodyData = json_decode($body);

        if ($bodyData->success !== true) {
            throw new UnprocessableEntity(
                'smsGatewayError',
                $body
            );
        }
    }
}
