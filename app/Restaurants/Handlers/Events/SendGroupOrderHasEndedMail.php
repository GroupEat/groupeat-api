<?php
namespace Groupeat\Restaurants\Handlers\Events;

use Groupeat\Auth\Services\GenerateAuthToken;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Events\GroupOrderHasEnded;
use Groupeat\Restaurants\Values\ConfirmationTokenDurationInMinutes;
use Groupeat\Support\Services\SendMail;
use Illuminate\Contracts\Routing\UrlGenerator;

class SendGroupOrderHasEndedMail
{
    private $mailer;
    private $urlGenerator;
    private $generateAuthToken;
    private $tokenDurationInMinutes;

    public function __construct(
        SendMail $mailer,
        UrlGenerator $urlGenerator,
        GenerateAuthToken $generateAuthToken,
        ConfirmationTokenDurationInMinutes $tokenDurationInMinutes
    ) {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->generateAuthToken = $generateAuthToken;
        $this->tokenDurationInMinutes = $tokenDurationInMinutes->value();
    }

    public function handle(GroupOrderHasEnded $groupOrderHasEnded)
    {
        $groupOrder = $groupOrderHasEnded->getGroupOrder();
        $groupOrder->load('orders.productFormats.product.type');
        $orders = $groupOrder->orders;
        $totalDiscountedPrice = formatPrice($groupOrder->totalDiscountedPrice);
        $confirmationUrl = $this->makeConfirmationUrl($groupOrder);

        $this->mailer->call(
            $groupOrder->restaurant->credentials,
            'restaurants::groupOrderHasEnded',
            'restaurants::groupOrders.ended.subject',
            compact('groupOrder', 'orders', 'totalDiscountedPrice', 'confirmationUrl')
        );
    }

    private function makeConfirmationUrl(GroupOrder $groupOrder)
    {
        $token = $this->generateAuthToken->call(
            $groupOrder->restaurant->credentials,
            $this->tokenDurationInMinutes
        );

        $id = $groupOrder->id;

        return $this->urlGenerator->to("groupOrders/$id/confirm?token=$token");
    }
}
