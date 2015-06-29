<?php
namespace Groupeat\Orders\Http\V1;

use Groupeat\Orders\Http\V1\Traits\CanAddOrder;
use Groupeat\Orders\Jobs\PushExternalOrder;
use Groupeat\Support\Http\V1\Abstracts\Controller;

class ExternalOrdersController extends Controller
{
    use CanAddOrder;

    public function push()
    {
        return $this->addOrder(function ($productFormats, $deliveryAddressData, $comment) {
            $customerData = $this->json('customer');

            return new PushExternalOrder(
                $this->auth->restaurant(),
                $customerData['firstName'],
                $customerData['lastName'],
                $customerData['phoneNumber'],
                $productFormats,
                $deliveryAddressData,
                $comment
            );
        });
    }
}
