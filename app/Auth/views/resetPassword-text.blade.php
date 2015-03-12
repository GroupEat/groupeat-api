@lang('auth::resetPassword.text')

{{ app('SendPasswordResetLinkService')->getUrl($token) }}
