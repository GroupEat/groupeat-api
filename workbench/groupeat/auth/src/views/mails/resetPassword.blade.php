@extends('layout.mails.main')

@section('firstLineWrapper')
@overwrite

@section('beforeButton')
    <p>@lang('auth::resetPassword.mail.text')</p>
@stop

@section('buttonId'){{ 'reset-password-link' }}@stop

@section('buttonUrl')
    {{ route('auth.showResetPasswordForm', compact('token')) }}
@stop

@section('button')
    @lang('auth::resetPassword.mail.button')
@stop

@section('afterButtonWrapper')
@overwrite

@section('footerWrapper')
@overwrite
