@lang('auth::resetPassword.text')

{{ app(\Groupeat\Auth\Services\SendPasswordResetLink::class)->getUrl($token) }}
