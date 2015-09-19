<?php
namespace Groupeat\Restaurants\Listeners;

use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Events\GroupOrderHasBeenClosed;
use Groupeat\Support\Listeners\Abstracts\QueuedListener;
use Groupeat\Support\Services\SendMail;
use Illuminate\Contracts\Routing\UrlGenerator;

class SendGroupOrderHasBeenClosedMail extends QueuedListener
{
    private $mailer;
    private $urlGenerator;

    public function __construct(
        SendMail $mailer,
        UrlGenerator $urlGenerator
    ) {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
    }

    public function handle(GroupOrderHasBeenClosed $event)
    {
        $groupOrder = $event->getGroupOrder();
        $groupOrder->load('orders.productFormats.product.type');
        $orders = $groupOrder->orders;
        $totalDiscountedPrice = formatPrice($groupOrder->totalDiscountedPrice);
        $confirmationUrl = $this->urlGenerator->to("groupOrders/{$groupOrder->id}/confirm");

        $this->mailer->call(
            $groupOrder->restaurant->credentials,
            'restaurants::groupOrderHasBeenClosed',
            'restaurants::groupOrders.ended.subject',
            compact('groupOrder', 'orders', 'totalDiscountedPrice', 'confirmationUrl')
        );
    }
}
