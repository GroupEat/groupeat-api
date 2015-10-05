<?php
namespace Groupeat\Admin\Listeners;

use Groupeat\Admin\Events\GroupOrderHasNotBeenConfirmed;
use Groupeat\Auth\Events\UserHasRegistered;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Orders\Events\GroupOrderHasBeenJoined;
use Groupeat\Support\Events\Abstracts\Event;
use Groupeat\Support\Listeners\Abstracts\QueuedListener;
use Groupeat\Support\Values\Environment;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class ShareNoteworthyEventsOnSlack extends QueuedListener
{
    const URL = 'https://hooks.slack.com/services/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';

    private $client;
    private $environment;
    private $logger;

    public function __construct(Environment $environment, LoggerInterface $logger)
    {
        $this->client = new Client;
        $this->environment = $environment;
        $this->logger = $logger;
    }

    public function handle(Event $event)
    {
        if ($this->environment->isProduction()) {
            $json = ['text' => (string) $event];

            try {
                $response = $this->client->post(static::URL, compact('json'));
            } catch (ClientException $e) {
                $this->logger->error("Could not share event [$event] on Slack.");
            }
        } else {
            $this->logger->debug("Event [$event] would have been shared on Slack.");
        }
    }
}
