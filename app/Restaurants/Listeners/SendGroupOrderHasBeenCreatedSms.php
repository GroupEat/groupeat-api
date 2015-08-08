<?php
namespace Groupeat\Restaurants\Listeners;

use Groupeat\Messaging\Services\SendSms;
use Groupeat\Messaging\Values\Sms;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Support\Listeners\Abstracts\QueuedListener;
use Groupeat\Support\Services\Locale;
use Psr\Log\LoggerInterface;

class SendGroupOrderHasBeenCreatedSms extends QueuedListener
{
    private $sendSms;
    private $locale;

    public function __construct(SendSms $sendSms, Locale $locale)
    {
        $this->sendSms = $sendSms;
        $this->locale = $locale;
    }

    public function handle(GroupOrderHasBeenCreated $event)
    {
        $order = $event->getOrder();

        if (!$order->isExternal()) {
            $groupOrder = $order->groupOrder;
            $restaurant = $groupOrder->restaurant;

            $this->locale->executeWithUserLocale(function () use ($groupOrder, $restaurant) {
                $phoneNumber = $restaurant->phoneNumber;
                $text = $this->locale->getTranslator()->get(
                    'restaurants::groupOrders.created.smsText',
                    ['creationTime' => $groupOrder->getPresenter()->creationTime]
                );

                $this->sendSms->call(new Sms($phoneNumber, $text));
            }, $restaurant->locale);
        }
    }
}
