@component('mail::message')

@lang('mail.invitation.message')

@component('mail::button', ['url' => $url])
@lang('mail.invitation.accept')
@endcomponent

@endcomponent
