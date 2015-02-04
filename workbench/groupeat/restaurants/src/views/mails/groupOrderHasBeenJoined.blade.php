@extends('layout.mails.simple')

@section('beforeButton')
    <p>
        @lang('restaurants::groupOrders.joined.whoAndWhen', [
            'customerFullName' => $customer->fullName,
            'groupOrderRef' => $groupOrder->reference,
            'creationTime' => $groupOrder->creationTime,
            'endingTime' => $groupOrder->endingTime,
        ])
    </p>

    <p>@lang('restaurants::groupOrders.orderedProducts', ['orderRef' => $order->reference])</p>

    {{ $order->productsTableForMail }}

    <p>@lang('restaurants::groupOrders.orderRawPrice', ['rawPrice' => $order->rawPrice])</p>

    <p>
        @lang('restaurants::groupOrders.deliveryAddress')
        <br>
        {{ $deliveryAddress }}
    </p>

    <p>@lang('restaurants::groupOrders.customerCanBeReached', ['phoneNumber' => $customer->phoneNumber ])</p>
@stop
