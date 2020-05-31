@extends('nova::auth.layout')

@section('content')

@include('nova::auth.partials.header')

<form
    class="bg-white shadow rounded-lg p-8 max-w-login mx-auto"
    method="POST"
    action="{{ route('verification.resend') }}">

    {{ csrf_field() }}

    @component('nova::auth.partials.heading')
        {{ __('Verify Your Email Address') }}
    @endcomponent

    @if (session('resent'))
    <div class="text-success text-center font-semibold my-3">
        {{ __('A fresh verification link has been sent to your email address.') }}
    </div>
    @endif

    @include('nova::auth.partials.errors')

    <div class="mb-6">
        <strong>{{ __('Before proceeding, please check your email for a verification link.') }}</strong>
    </div>

    <div class="mb-6">
        {{ __('If you did not receive the email, click the Resend Button below.') }}
    </div>

    <button class="w-full btn btn-default btn-primary hover:bg-primary-dark" type="submit">
        {{ __('Resend') }}
    </button>

</form>

@endsection
