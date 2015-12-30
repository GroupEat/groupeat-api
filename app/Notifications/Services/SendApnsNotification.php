<?php
namespace Groupeat\Notifications\Services;

use Groupeat\Notifications\Values\ApnsCertificate;
use Groupeat\Notifications\Values\Notification;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Groupeat\Support\Services\Locale;

class SendApnsNotification
{
    const URL = 'ssl://gateway.sandbox.push.apple.com:2195';
    const TIMEOUT_IN_SECONDS = 60.0;

    private $certificate;

    public function __construct(ApnsCertificate $certificate)
    {
        $this->certificate = $certificate;
    }

    public function call(Notification $notification): int
    {
        $streamContext = stream_context_create();
        stream_context_set_option($streamContext, 'ssl', 'local_cert', $this->certificate->getPath());
        stream_context_set_option($streamContext, 'ssl', 'passphrase', $this->certificate->getPassphrase());

        // Open a connection to the APNS server
        $ApnsConnection = stream_socket_client(
            static::URL,
            $errorNumber,
            $errorMessage,
            static::TIMEOUT_IN_SECONDS,
            STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT,
            $streamContext
        );

        if (!$ApnsConnection) {
            throw new UnprocessableEntity(
                'apnsError',
                "[$errorNumber] $errorMessage"
            );
        }

        $data = [
            'aps' => [
                'alert' => $notification->getTitle(),
            ],
        ];

        if ($notification->getTimeToLiveInSeconds()) {
            $data['pn_ttl'] = $notification->getTimeToLiveInSeconds();
        }

        if ($notification->isSilent()) {
            $data['aps']['content-available'] = 1;
        }

        foreach ($notification->getAdditionalData() as $key => $value) {
            $data[$key] = $value;
        }

        $message = $this->getBinaryMessage($notification->getDevice()->notificationToken, $data);

        $status = fwrite($ApnsConnection, $message, strlen($message));
        fclose($ApnsConnection);

        if ($status === false) {
            throw new UnprocessableEntity(
                'apnsError',
                "Failed to send the request to APNS. Status: $status"
            );
        }

        return $status;
    }

    private function getBinaryMessage(string $notificationToken, array $data): string
    {
        $payload = json_encode($data);

        return chr(0)
            . pack('n', 32)
            . pack('H*', $notificationToken)
            . pack('n', strlen($payload))
            . $payload;
    }
}
