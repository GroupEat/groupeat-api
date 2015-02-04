<?php namespace Groupeat\Restaurants\Services;

use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Services\SendMail;

class SendGroupOrderHasEndedMail {

    /**
     * @var SendMail
     */
    private $mailer;


    public function __construct(SendMail $mailer)
    {
        $this->mailer = $mailer;
    }

    public function call(GroupOrder $groupOrder)
    {
        $groupOrder->load('orders.productFormats.product.type');
        $orders = $groupOrder->orders;
        $totalReducedPrice = formatPriceWithCurrency($groupOrder->totalReducedPrice);

        return $this->mailer->call(
            $groupOrder->restaurant->credentials,
            'restaurants::mails.groupOrderHasEnded',
            'restaurants::groupOrders.ended.subject',
            compact('groupOrder', 'orders', 'totalReducedPrice')
        );
    }

}
