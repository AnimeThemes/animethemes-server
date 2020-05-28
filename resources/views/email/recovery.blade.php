@component('mail::message')

# @lang('nova.recovery_codes_header')

@lang('nova.recovery_codes_description')

## @lang('nova.recovery_codes')

@lang('nova.recovery_codes_help')

@component('mail::panel')
    @foreach ($recoveryCodes as $recoveryCode)
        {{ $recoveryCode['code'] }}
    @endforeach
@endcomponent

@component('mail::subcopy')
    ### @lang('nova.recovery_codes_generate')

    @lang('nova.recovery_codes_generate_description')
@endcomponent

@endcomponent