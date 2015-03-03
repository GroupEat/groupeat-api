@lang('auth::resetPassword.mail.text')

{{ app('SendPasswordResetLinkService')->getUrl($token) }}
