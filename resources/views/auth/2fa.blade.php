@extends('nova::auth.layout')

@section('content')

@include('nova::auth.partials.header')

<style>
  svg {
    display: block;
    margin-left: auto;
    margin-right: auto;
  }
</style>

<form
    class="bg-white shadow rounded-lg p-8 max-w-login mx-auto"
    method="POST"
    action="{{ route('2fa.store') }}">

    {{ csrf_field() }}

    @component('nova::auth.partials.heading')
        @lang('nova.2fa_enable_header')
    @endcomponent


    <div class="mb-6">
      <strong>@lang('nova.2fa_enable_step_one')</strong>
      <p>@lang('nova.2fa_enable_step_one_description')</p>
    </div>
    <div class="mb-6">
      {!! $qrCode !!}
    </div>

    <div class="mb-6">
      <label>@lang('nova.2fa_enable_step_one_barcode_help')</label>
    </div>
    <div class="mb-6">
      <p>{{ $asString }}</p>
    </div>

    <div class="mb-6">
      <label><strong>@lang('nova.2fa_enable_step_two')</strong></label>
      <p>@lang('nova.2fa_enable_step_two_description')</p>
    </div>

    <div class="mb-6">
      <input class="form-control form-input form-input-bordered w-full" type="text" name="token" placeholder="@lang('nova.2fa_enable_step_two_placeholder')" required autofocus class="form-control{{ $errors->has('token') ? ' is-invalid' : '' }}"
        id="token"> @if ($errors->has('token'))
      <span class="invalid-feedback" role="alert">
        <strong>{{ $errors->first('token') }}</strong>
      </span> @endif
    </div>

    <button class="w-full btn btn-default btn-primary hover:bg-primary-dark" type="submit">
      @lang('nova.verify')
    </button>

</form>

@endsection