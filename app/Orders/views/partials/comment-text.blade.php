@if($order->comment)
    @lang('orders::orders.attachedComment')
    {{{ $order->comment }}}.
@endif
