<?php
namespace Groupeat\Restaurants\Listeners;

use Groupeat\Auth\Services\GenerateToken;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Events\GroupOrderHasBeenClosed;
use Groupeat\Restaurants\Values\ConfirmationTokenDurationInMinutes;
use Groupeat\Support\Listeners\Abstracts\QueuedHandler;
use Groupeat\Support\Services\SendMail;
use Illuminate\Contracts\Routing\UrlGenerator;

class SendGroupOrderHasBeenClosedMail extends QueuedHandler
{
    private $mailer;
    private $urlGenerator;
    private $generateToken;
    private $tokenDurationInMinutes;

    public function __construct(
        SendMail $mailer,
        UrlGenerator $urlGenerator,
        GenerateToken $generateToken,
        ConfirmationTokenDurationInMinutes $tokenDurationInMinutes
    ) {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->generateToken = $generateToken;
        $this->tokenDurationInMinutes = $tokenDurationInMinutes->value();
    }

    public function handle(GroupOrderHasBeenClosed $groupOrderHasBeenClosed)
    {
        $groupOrder = $groupOrderHasBeenClosed->getGroupOrder();
        $groupOrder->load('orders.productFormats.product.type');
        $orders = $groupOrder->orders;
        $totalDiscountedPrice = formatPrice($groupOrder->totalDiscountedPrice);
        $confirmationUrl = $this->makeConfirmationUrl($groupOrder);

        $this->mailer->call(
            $groupOrder->restaurant->credentials,
            'restaurants::groupOrderHasBeenClosed',
            'restaurants::groupOrders.ended.subject',
            compact('groupOrder', 'orders', 'totalDiscountedPrice', 'confirmationUrl')
        );
    }

    private function makeConfirmationUrl(GroupOrder $groupOrder)
    {
        $token = $this->generateToken->call(
            $groupOrder->restaurant->credentials,
            $this->tokenDurationInMinutes
        );

        $id = $groupOrder->id;

        return $this->urlGenerator->to("groupOrders/$id/confirm?token=$token");
    }
}
