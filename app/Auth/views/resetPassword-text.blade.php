@inject('getResetPasswordUrl', 'Groupeat\Auth\Services\GetResetPasswordUrl')

@lang('auth::resetPassword.text')

{!! $getResetPasswordUrl->call($token) !!}
