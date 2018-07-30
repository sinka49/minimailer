@extends('backend::layouts.base')

@section('content')
    <div class="row">
        <div class="col-md-12 text-center" style="margin-left: 15% !important; margin-bottom: 30px;">
            <img width="400px" class="logo-image" src="/images/Design-png.png">
        </div>
        <div class="col-md-12">
            <div class="col-md-3" style="padding-top: 20px;padding-bottom: 20px;">
                @include('backend::layouts.dash-menu')
            </div>
            <div class="col-md-9">
                @yield('page-content')
            </div>
        </div>
    </div>
@endsection
