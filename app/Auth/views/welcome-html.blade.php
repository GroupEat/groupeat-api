@extends('layout.main')

@section('mailId'){{ 'auth.welcome' }}@stop

@section('beforeButton')
    <p>@lang('auth::welcome.content')</p>
@stop
