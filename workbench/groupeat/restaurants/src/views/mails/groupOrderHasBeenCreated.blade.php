@extends('layout.mails.simple')

@section('beforeButton')
    <p>
        @lang('restaurants::groupOrderHasBeenCreated.whoAndWhen', [
            'customerFullName' => $customer->fullName,
            'creationTime' => $groupOrder->creationTime,
            'endingTime' => $groupOrder->endingTime,
        ])
    </p>

    <p>@lang('restaurants::groupOrderHasBeenCreated.orderedProducts')</p>

    {{ $order->htmlTableForEmail }}

    <p>@lang('restaurants::groupOrderHasBeenCreated.orderRawPrice', ['rawPrice' => $order->rawPrice])</p>

    <p>
        @lang('restaurants::groupOrderHasBeenCreated.deliveryAddress')
        <br>
        {{ $deliveryAddress }}
    </p>

    <p>@lang('restaurants::groupOrderHasBeenCreated.customerCanBeReached', ['phoneNumber' => $customer->phoneNumber ])</p>
@stop
