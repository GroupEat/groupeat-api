@lang("restaurants::groupOrders.$action.whoAndWhen", [
    'customerFullName' => $customer->fullName,
    'groupOrderRef' => $groupOrder->reference,
    'creationTime' => $groupOrder->creationTime,
    'endingTime' => $groupOrder->endingTime,
])

@lang('restaurants::groupOrders.orderedProducts', ['orderRef' => $order->reference])

{{ $order->productsListAsPlainText }}.

@include('orders::mails.partials.comment-text')

@lang('restaurants::groupOrders.orderRawPrice', ['rawPrice' => $order->rawPrice])

@lang('restaurants::groupOrders.deliveryAddress')

{{ $deliveryAddress }}

@lang('restaurants::groupOrders.customerCanBeReached', ['phoneNumber' => $customer->phoneNumber ])
