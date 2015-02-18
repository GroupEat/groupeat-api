@lang('customers::orders.confirmed.indication', [
    'orderRef' => $order->reference,
    'restaurantName' => '<i>'.$restaurant->name.'</i>',
    'preparedAtTime' => $groupOrder->preparedAtTime,
 ])

{{ $order->presentDetailsTableForCustomerAsPlainText() }}.

@lang('customers::orders.confirmed.summary')

{{ $order->presentProductsListAsPlainText(false) }}.

@include('orders::mails.partials.comment-text')

@lang('customers::orders.confirmed.discountRateAndPrices', [
    'discountRate' => $groupOrder->discountRate,
    'orderDiscountedPrice' => '<b>'.$order->discountedPrice.'</b>',
    'orderRawPrice' => $order->rawPrice,
])

@lang('customers::orders.confirmed.contactRestaurant', [
     'restaurantEmail' => $restaurant->email,
     'restaurantPhoneNumber' => $restaurant->phoneNumber,
 ])
