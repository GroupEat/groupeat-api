@lang('auth::resetPassword.mail.text')

{{ route('auth.showResetPasswordForm', compact('token')) }}
