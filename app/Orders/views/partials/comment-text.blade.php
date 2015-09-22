@if($order->comment)
    @lang('orders::orders.attachedComment')
    {!! strip_tags($order->comment) !!}.
@endif
