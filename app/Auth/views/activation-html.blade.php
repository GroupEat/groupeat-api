@extends('layout.main')

@section('mailId'){{ 'auth.activation' }}@stop

@section('firstLine')
    @lang('auth::activation.welcome')
@stop

@section('beforeButton')
    <p>@lang('auth::activation.indication')</p>
@stop

@section('buttonId'){{ 'activation-link' }}@stop

@section('buttonUrl')
    {{ $url }}
@stop

@section('button')
    @lang('auth::activation.button')
@stop

@section('afterButtonWrapper')
@overwrite

@section('footerWrapper')
@overwrite
