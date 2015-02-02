<?php $url = route('auth.showResetPasswordForm', compact('token')); ?>
<p>@lang('auth::resetPassword.mail.text')</p>
<p><a id="reset-password-link" href="{{ $url }}">{{ $url }}</a></p>
