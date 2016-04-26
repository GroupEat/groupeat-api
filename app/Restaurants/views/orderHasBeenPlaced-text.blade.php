@lang("restaurants::groupOrders.$action.whoAndWhen", [
    'customerPhoneNumber' => $customer->phoneNumber,
    'groupOrderRef' => $groupOrder->reference,
    'creationTime' => $groupOrder->creationTime,
    'endingTime' => $groupOrder->endingTime,
])

@lang('restaurants::groupOrders.orderedProducts', ['orderRef' => $order->reference])

{!! $order->productsListAsPlainText !!}.

@include('orders::partials.comment-text')

@lang('restaurants::groupOrders.orderRawPrice', ['rawPrice' => $order->rawPrice])

@lang('restaurants::groupOrders.deliveryAddress')

{!! strip_tags($deliveryAddress) !!}

@lang('restaurants::groupOrders.customerCanBeReached', ['phoneNumber' => $customer->phoneNumber ])
