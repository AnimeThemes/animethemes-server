@extends('nova::auth.layout')

@section('content')

@include('nova::auth.partials.header')

<form
    class="bg-white shadow rounded-lg p-8 max-w-login mx-auto"
    method="POST"
    action="{{ route('register') }}">

    {{ csrf_field() }}
    
    <input type="hidden" name="token" value="{{ $invitation->token }}">
    <input type="hidden" name="name" value="{{ $invitation->name }}">
    <input type="hidden" name="email" value="{{ $invitation->email }}">

    @component('nova::auth.partials.heading')
        {{ __('Register') }}
    @endcomponent

    @if (session('status'))
    <div class="text-success text-center font-semibold my-3">
        {{ session('status') }}
    </div>
    @endif

    @include('nova::auth.partials.errors')

    <div class="mb-6">
        <label class="block font-bold mb-2" for="name">{{ __('Name') }}</label>
        {{ $invitation->name }}
    </div>

    <div class="mb-6">
        <label class="block font-bold mb-2" for="email">{{ __('E-Mail Address') }}</label>
        {{ $invitation->email }}
    </div>

    <div class="mb-6 {{ $errors->has('password') ? ' has-error' : '' }}">
        <label class="block font-bold mb-2" for="password">{{ __('Password') }}</label>
        <input class="form-control form-input form-input-bordered w-full" id="password" type="password" name="password" required>
    </div>

    <div class="mb-6 {{ $errors->has('password-confirm') ? ' has-error' : '' }}">
        <label class="block font-bold mb-2" for="password-confirm">{{ __('Confirm Password') }}</label>
        <input class="form-control form-input form-input-bordered w-full" id="password-confirm" type="password" name="password_confirmation" required>
    </div>

    <button class="w-full btn btn-default btn-primary hover:bg-primary-dark" type="submit">
        {{ __('Register') }}
    </button>

</form>

@endsection
