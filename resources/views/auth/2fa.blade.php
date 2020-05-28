@extends('layouts.auth')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">@lang('nova.2fa_enable_header')</div>
        <div class="card-body">
          <form action="{{ route('2fa.store') }}" method="post">
            @csrf
            <div class="form-group">
                <strong>@lang('nova.2fa_enable_step_one')</strong>
                <p>@lang('nova.2fa_enable_step_one_description')</p>
                {!! $qrCode !!}
            </div>
            <div class="form-group">
                <label>@lang('nova.2fa_enable_step_one_barcode_help')</label>
                <p>{{ $asString }}</p>
            </div>
            <div class="form-group">
              <label><strong>@lang('nova.2fa_enable_step_two')</strong></label>
              <p>@lang('nova.2fa_enable_step_two_description')</p>
              <input type="text" name="token" placeholder="@lang('nova.2fa_enable_step_two_placeholder')" class="form-control{{ $errors->has('token') ? ' is-invalid' : '' }}"
                id="token"> @if ($errors->has('token'))
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $errors->first('token') }}</strong>
                </span> @endif
            </div>

            <button class="btn btn-primary btn-large">@lang('nova.verify')</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection