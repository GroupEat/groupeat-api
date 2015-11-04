<?php
namespace Groupeat\Admin\Listeners;

use Groupeat\Admin\Events\GroupOrderHasNotBeenConfirmed;
use Groupeat\Admin\Values\SlackBroadcastingEnabled;
use Groupeat\Auth\Events\UserHasRegistered;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Orders\Events\GroupOrderHasBeenJoined;
use Groupeat\Support\Events\Abstracts\Event;
use Groupeat\Support\Listeners\Abstracts\QueuedListener;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class BroadcastOnSlack extends QueuedListener
{
    const URL = 'https://hooks.slack.com/services/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';

    private $logger;
    private $enabled;
    private $client;

    public function __construct(LoggerInterface $logger, SlackBroadcastingEnabled $enabled)
    {
        $this->logger = $logger;
        $this->enabled = $enabled->value();
        $this->client = new Client;
    }

    public function handle(Event $event)
    {
        if ($this->enabled) {
            $json = ['text' => (string) $event];

            try {
                $response = $this->client->post(static::URL, compact('json'));
            } catch (ClientException $e) {
                $this->logger->error("Could not broadcast event [$event] on Slack.");
            }
        } else {
            $this->logger->debug("Event [$event] would have been broadcast on Slack.");
        }
    }
}
