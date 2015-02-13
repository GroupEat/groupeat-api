@extends('layout.mails.main')

@section('mailId'){{ 'restaurants.groupOrderHasEnded' }}@stop

@section('firstLineWrapper')
@overwrite

@section('beforeButton')
    <p>
        @lang('restaurants::groupOrders.ended.indication', [
            'groupOrderRef' => $groupOrder->reference,
            'creationTime' => $groupOrder->creationTime,
        ])
        <br>
        @choice('restaurants::groupOrders.ended.composedOf', $orders->count())
    </p>

    @foreach($orders as $order)
        {{ $order->summaryForMail }}
        <br><br>
        <hr>
        <br>
    @endforeach

    <h2>
        @lang('orders::groupOrders.summary')
    </h2>

    @if ($orders->count() > 1)
        {{ $groupOrder->productsTableForMail }}
    @endif

    <p>
        <b>
        @lang('restaurants::groupOrders.ended.discountAndPrice', [
            'discountRate' => $groupOrder->discountRate,
            'totalDiscountedPrice' => $totalDiscountedPrice,
        ])
        </b>
    </p>
@stop

@section('buttonId'){{ 'confirm-group-order-link' }}@stop

@section('buttonUrl')
    {{ $confirmationUrl }}
@stop

@section('button')
    @lang('orders::confirmation.mail.button')
@stop
