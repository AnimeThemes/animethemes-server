@component('mail::message')

@lang('nova.invitation_message')

@component('mail::button', ['url' => $url])
@lang('nova.invitation_accept')
@endcomponent

@endcomponent