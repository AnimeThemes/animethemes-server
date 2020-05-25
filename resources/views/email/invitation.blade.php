@component('mail::message')
# Invitation

You have been invited to join AnimeThemes!

@component('mail::button', ['url' => $url])
Accept Invitation
@endcomponent

@endcomponent