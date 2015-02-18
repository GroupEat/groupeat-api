@if($order->comment)
    <p>
        @lang('orders::orders.attachedComment')
        <br>
        <i>{{{ $order->comment }}}</i>
    </p>
@endif
