<?php
namespace Groupeat\Orders\Http\V1\Traits;

use Closure;
use Groupeat\Support\Exceptions\BadRequest;
use Symfony\Component\HttpFoundation\Response;

trait CanAddOrder
{
    protected function addOrder(Closure $getCommandCallback)
    {
        $productFormats = $this->json('productFormats');

        if (empty($productFormats)) {
            throw new BadRequest(
                'missingProductFormats',
                "The product formats object is required to place an order"
            );
        }

        $deliveryAddressData = $this->json('deliveryAddress');

        if (empty($deliveryAddressData)) {
            throw new BadRequest(
                'missingDeliveryAddress',
                "The delivery address object is required to place an order"
            );
        }

        $comment = $this->json('comment');

        $command = $getCommandCallback($productFormats, $deliveryAddressData, $comment);
        $order = $this->dispatch($command);

        return $this->itemResponse($order)->setStatusCode(Response::HTTP_CREATED);
    }
}
