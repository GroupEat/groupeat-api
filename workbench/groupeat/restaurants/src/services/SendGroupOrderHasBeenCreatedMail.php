<?php namespace Groupeat\Restaurants\Services;

use Groupeat\Orders\Entities\Order;
use Groupeat\Support\Services\Locale;
use Illuminate\Mail\Mailer;

class SendGroupOrderHasBeenCreatedMail {

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var Locale
     */
    private $localeService;


    public function __construct(Mailer $mailer, Locale $localeService)
    {
        $this->mailer = $mailer;
        $this->localeService = $localeService;
    }

    public function call(Order $order)
    {
        $order->productFormats->load('product');

        $view = 'restaurants::mails.groupOrderHasBeenCreated';
        $customer = $order->customer;
        $deliveryAddress = $order->deliveryAddress;
        $groupOrder = $order->groupOrder;
        $restaurant = $groupOrder->restaurant;
        $email = $restaurant->email;
        $data = compact('order', 'customer', 'deliveryAddress', 'groupOrder');

        return $this->localeService->executeWithUserLocale(function() use ($view, $data, $email)
        {
            $this->mailer->send($view, $data, function($message) use ($email)
            {
                $subject = $this->localeService->getTranslator()
                    ->get('restaurants::groupOrderHasBeenCreated.subject');

                $message->to($email)->subject($subject);
            });
        }, $restaurant->locale);
    }

}
