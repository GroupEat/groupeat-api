<?php
namespace Groupeat\Restaurants\Services;

use Groupeat\Auth\Services\GenerateAuthToken;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Services\SendMail;
use Illuminate\Routing\UrlGenerator;

class SendGroupOrderHasEndedMail
{
    /**
     * @var SendMail
     */
    private $mailer;

    /**
     * @var UrlGenerator
     */
    private $urlGenerator;

    /**
     * @var GenerateAuthToken
     */
    private $tokenGenerator;

    /**
     * @var int
     */
    private $tokenTtlInMinutes;

    public function __construct(
        SendMail $mailer,
        UrlGenerator $urlGenerator,
        GenerateAuthToken $tokenGenerator,
        $tokenTtlInMinutes
    ) {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->tokenGenerator = $tokenGenerator;
        $this->tokenTtlInMinutes = (int) $tokenTtlInMinutes;
    }

    /**
     * @param GroupOrder $groupOrder
     */
    public function call(GroupOrder $groupOrder)
    {
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
        $token = $this->tokenGenerator->forUser(
            $groupOrder->restaurant->credentials,
            $this->tokenTtlInMinutes
        );

        $id = $groupOrder->id;

        return $this->urlGenerator->to("groupOrders/$id/confirm?token=$token");
    }
}
