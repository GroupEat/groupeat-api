@if($order->comment)
    <p>
        @lang('orders::orders.attachedComment')
        <br>
        <i>{!! strip_tags($order->comment) !!}</i>
    </p>
@endif
