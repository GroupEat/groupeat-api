@extends('layout.mails.main')

@section('mailId'){{ 'customers.orderHasBeenConfirmed' }}@stop

@section('firstLine')
    @lang('customers::orders.confirmed.thanks')
@stop

@section('beforeButton')
    <p>
        @lang('customers::orders.confirmed.indication', [
            'orderRef' => $order->reference,
            'restaurantName' => '<i>'.$restaurant->name.'</i>',
            'preparedAtTime' => $groupOrder->preparedAtTime,
         ])
    </p>

    {{ $order->presentDetailsTableForCustomerForMail() }}

    <p>@lang('customers::orders.confirmed.summary')</p>

    {{ $order->presentProductsTableForMail(false) }}

    @include('orders::mails.partials.comment-html')

    <p>
        @lang('customers::orders.confirmed.discountRateAndPrices', [
            'discountRate' => $groupOrder->discountRate,
            'orderDiscountedPrice' => '<b>'.$order->discountedPrice.'</b>',
            'orderRawPrice' => $order->rawPrice,
        ])
    </p>

    <p>
        @lang('customers::orders.confirmed.contactRestaurant', [
             'restaurantEmail' => $restaurant->mailTo,
             'restaurantPhoneNumber' => $restaurant->phoneNumber,
         ])
    </p>
@stop

@section('buttonWrapper')
@overwrite

@section('afterButtonWrapper')
@overwrite

@section('footerWrapper')
@overwrite
