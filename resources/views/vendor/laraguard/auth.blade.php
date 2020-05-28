@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Two Factor Authentication Required') }}</div>
                <div class="card-body">
                    <form action="{{ $action }}" method="post">
                        @csrf
                        @foreach($credentials as $name => $value)
                            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                        @endforeach
                        @if($remember)
                            <input type="hidden" name="remember" value="on">
                        @endif

                        <p class="text-center">
                            {{ __('To log in, open up your Authenticator app and issue the 6-digit code.') }}
                        </p>
                        <div class="form-row justify-content-center py-3">
                            <div class="col-sm-8 col-8 mb-3">
                                <input type="text" name="2fa_code" id="2fa_code"
                                    class="@if($error) is-invalid @endif form-control form-control-lg"
                                    minlength="6" placeholder="123456" required>
                                @if($error)
                                    <div class="invalid-feedback">
                                        {{ __('The Code is invalid or has expired.') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">{{ __('Log in') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
