@extends('layout.simple')

@section('mailId'){{ 'restaurants.orderHasBeenPlaced' }}@stop

@section('beforeButton')
    <p>
        @lang("restaurants::groupOrders.$action.whoAndWhen", [
            'customerPhoneNumber' => $customer->phoneNumber,
            'groupOrderRef' => $groupOrder->reference,
            'creationTime' => $groupOrder->creationTime,
            'endingTime' => $groupOrder->endingTime,
        ])
    </p>

    <p>@lang('restaurants::groupOrders.orderedProducts', ['orderRef' => $order->reference])</p>

    {!! $order->productsTableForMail !!}

    @include('orders::partials.comment-html')

    <p>@lang('restaurants::groupOrders.orderRawPrice', ['rawPrice' => $order->rawPrice])</p>

    <p>
        @lang('restaurants::groupOrders.deliveryAddress')
        <br>
        {!! strip_tags($deliveryAddress) !!}
    </p>

    <p>@lang('restaurants::groupOrders.customerCanBeReached', ['phoneNumber' => $customer->phoneNumber ])</p>
@stop
