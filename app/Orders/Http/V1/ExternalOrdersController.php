<?php
namespace Groupeat\Orders\Http\V1;

use Groupeat\Orders\Http\V1\Traits\CanAddOrder;
use Groupeat\Orders\Jobs\PushExternalOrder;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Http\V1\Abstracts\Controller;
use Groupeat\Support\Values\PhoneNumber;

class ExternalOrdersController extends Controller
{
    use CanAddOrder;

    public function push(Restaurant $restaurant)
    {
        $this->auth->assertSame($restaurant);

        return $this->addOrder(function ($productFormats, $deliveryAddressData, $comment) {
            $customerData = $this->json('customer');

            return new PushExternalOrder(
                $this->auth->restaurant(),
                $productFormats,
                $deliveryAddressData,
                $comment,
                $customerData['phoneNumber'] ? new PhoneNumber($customerData['phoneNumber']) : null
            );
        });
    }
}
