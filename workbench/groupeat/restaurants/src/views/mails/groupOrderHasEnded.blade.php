@extends('layout.mails.simple')

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
        @lang('restaurants::groupOrders.ended.reductionAndPrice', [
            'reductionRate' => (100 * $groupOrder->reduction).'%',
            'totalReducedPrice' => $totalReducedPrice,
        ])
        </b>
    </p>
@stop
