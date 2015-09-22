@lang('auth::resetPassword.text')

{!! app(\Groupeat\Auth\Jobs\SendPasswordResetLinkHandler::class)->getUrl($token) !!}
