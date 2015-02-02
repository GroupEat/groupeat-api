@include('restaurants::mails.partials.simple-table')

<p>
@lang('restaurants::groupOrderHasBeenCreated.whoAndWhen', [
    'customerFullName' => $customer->fullName,
    'creationTime' => $groupOrder->creationTime,
    'endingTime' => $groupOrder->endingTime,
])
</p>

<p>@lang('restaurants::groupOrderHasBeenCreated.orderedProducts')</p>

{{ $order->htmlTable }}

<p>@lang('restaurants::groupOrderHasBeenCreated.orderRawPrice', ['rawPrice' => $order->rawPrice])</p>

<p>@lang('restaurants::groupOrderHasBeenCreated.deliveryAddress', compact('deliveryAddress'))</p>

<p>@lang('restaurants::groupOrderHasBeenCreated.customerCanBeReached', ['phoneNumber' => $customer->phoneNumber ])</p>
