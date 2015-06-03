<?php
namespace Groupeat\Notifications\Services;

use Groupeat\Notifications\Entities\Notification;
use Groupeat\Notifications\Services\Abstracts\NotificationSender;
use Groupeat\Notifications\Values\ApnsCertificate;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Groupeat\Support\Services\Locale;
use Psr\Log\LoggerInterface;

class SendApnsNotification extends NotificationSender
{
    const URL = 'ssl://gateway.sandbox.push.apple.com:2195';
    const TIMEOUT_IN_SECONDS = 60.0;

    private $certificate;

    public function __construct(Locale $locale, LoggerInterface $logger, ApnsCertificate $certificate)
    {
        parent::__construct($locale, $logger);

        $this->certificate = $certificate;
    }

    public function call(Notification $notification)
    {
        $customer = $notification->customer;
        $device = $notification->device;
        $groupOrder = $notification->groupOrder;

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
                'alert' => $this->translateFor('joinGroupOrder', $customer->credentials),
            ],
            'groupOrderId' => $groupOrder->id,
        ];

        $message = $this->getBinaryMessage($device->notificationToken, $data);

        $status = fwrite($ApnsConnection, $message, strlen($message));
        fclose($ApnsConnection);

        if ($status === false) {
            throw new UnprocessableEntity(
                'apnsError',
                "Failed to send the request to APNS."
            );
        }
    }

    private function getBinaryMessage($notificationToken, $data)
    {
        $payload = json_encode($data);

        return chr(0)
            . pack('n', 32)
            . pack('H*', $notificationToken)
            . pack('n', strlen($payload))
            . $payload;
    }
}
