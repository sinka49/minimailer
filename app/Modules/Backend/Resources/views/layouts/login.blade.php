@extends('backend::layouts.base')

@section('content')
    <div class="col-md-12 text-center">
        <img width="400px" class="logo-image" src="/images/Design-png.png">
    </div>
    <div class="col-md-4 col-md-offset-4 form-login-magrin-top">
        <h3 class="text-center">{{ $formTitle  }}</h3>
        <hr/>
        @if( isset($errorMessage) && !empty($errorMessage) )
            <p class="text-danger">{{$errorMessage}}</p>
        @endif
        @if( isset($successMessage) && !empty($successMessage) )
            <p class="text-success">{{$successMessage}}</p>
        @endif

        @yield('login-form')
    </div>
@endsection
