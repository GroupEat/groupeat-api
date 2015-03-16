@lang('auth::resetPassword.text')

{{ app(\Groupeat\Auth\Handlers\Commands\SendPasswordResetLinkHandler::class)->getUrl($token) }}
