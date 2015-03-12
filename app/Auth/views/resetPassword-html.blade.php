@extends('layout.main')

@section('mailId'){{ 'auth.resetPassword' }}@stop

@section('firstLineWrapper')
@overwrite

@section('beforeButton')
    <p>@lang('auth::resetPassword.text')</p>
@stop

@section('buttonId'){{ 'password-reset-link' }}@stop

@section('buttonUrl')
    {{ app('SendPasswordResetLinkService')->getUrl($token) }}
@stop

@section('button')
    @lang('auth::resetPassword.button')
@stop

@section('afterButtonWrapper')
@overwrite

@section('footerWrapper')
@overwrite
