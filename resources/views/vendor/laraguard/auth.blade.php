@extends('nova::auth.layout')

@section('content')

@include('nova::auth.partials.header')

<form
    class="bg-white shadow rounded-lg p-8 max-w-login mx-auto"
    method="POST"
    action="{{ $action }}">

    @csrf
    @foreach($credentials as $name => $value)
        <input type="hidden" name="{{ $name }}" value="{{ $value }}">
    @endforeach

    @component('nova::auth.partials.heading')
        {{ __('Two Factor Authentication Required') }}
    @endcomponent

    <div class="mb-6">
        <p class="text-center">
            {{ __('To log in, open up your Authenticator app and issue the 6-digit code.') }}
        </p>
    </div>

    <div class="mb-6">
        <input class="form-control form-input form-input-bordered w-full" type="text" name="2fa_code" id="2fa_code" required autofocus>
        @if($error)
        <div class="text-center font-semibold text-danger my-3">
            {{ __('The Code is invalid or has expired.') }}
        </div>
        @endif    
    </div>

    <button class="w-full btn btn-default btn-primary hover:bg-primary-dark" type="submit">
        {{ __('Log in') }}
    </button>

</form>

@endsection
