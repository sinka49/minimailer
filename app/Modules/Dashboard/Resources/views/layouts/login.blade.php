@extends('dashboard::layouts.base')

@section('content')
    <div class="logo">
        <img width="350px" class="logo-image" src="/images/Design-png.png">
    </div>
    <div class="login_form">
        @yield('link')


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
