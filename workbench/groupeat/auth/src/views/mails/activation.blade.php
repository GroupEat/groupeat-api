@extends('layout.mails.main')

@section('firstLine')
    @lang('auth::activation.mail.welcome')
@stop

@section('beforeButton')
    <p>@lang('auth::activation.mail.indication')</p>
@stop

@section('buttonId'){{ 'activation-link' }}@stop

@section('buttonLink')
    {{ $url }}
@stop

@section('button')
    @lang('auth::activation.mail.button')
@stop

@section('afterButtonWrapper')
@overwrite

@section('footerWrapper')
@overwrite
