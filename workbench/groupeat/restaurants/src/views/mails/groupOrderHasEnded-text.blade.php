@lang('restaurants::groupOrders.ended.indication', [
    'groupOrderRef' => $groupOrder->reference,
    'creationTime' => $groupOrder->creationTime,
])

@choice('restaurants::groupOrders.ended.composedOf', $orders->count())

@foreach($orders as $order)
    {{ $order->summaryAsPlainText }}
@endforeach

@lang('orders::groupOrders.summary')

@if ($orders->count() > 1)
    {{ $groupOrder->productsTableForMail }}
@endif

@lang('restaurants::groupOrders.ended.discountAndPrice', [
    'discountRate' => $groupOrder->discountRate,
    'totalDiscountedPrice' => $totalDiscountedPrice,
])

{{ $confirmationUrl }}