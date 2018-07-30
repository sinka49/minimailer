@php( $formTitle = 'Reset Password')

@extends('dashboard::layouts.login')

@section('login-form')
    @parent
    <form class="form-horizontal" role="form" method="POST" action="{{ route('password.email') }}">
        {{ csrf_field() }}
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    {!! BootForm::text('email', 'Email', old('email'),['placeholder' => 'example@mail.com', 'required']) !!}
    {!! BootForm::button('Send Password Reset Link', ['class'=>'btn btn-primary btn-sm pull-right', 'type'=>'submit', 'name'=>'forgotpass']) !!}
    {{ BootForm::close() }}
@endsection