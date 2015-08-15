<?php
namespace Groupeat\Orders\Http\V1\Traits;

use Closure;
use Symfony\Component\HttpFoundation\Response;

trait CanAddOrder
{
    protected function addOrder(Closure $getCommandCallback)
    {
        $productFormats = $this->json('productFormats');
        $deliveryAddressData = $this->json('deliveryAddress');
        $comment = $this->json('comment');

        $command = $getCommandCallback($productFormats, $deliveryAddressData, $comment);
        $order = $this->dispatch($command);

        return $this->itemResponse($order)->setStatusCode(Response::HTTP_CREATED);
    }
}
