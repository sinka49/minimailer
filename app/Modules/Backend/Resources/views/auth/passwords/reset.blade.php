@php( $formTitle = 'Reset Password')

@extends('dashboard::layouts.login')

@section('login-form')
    @parent
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <form class="form-horizontal" role="form" method="POST" action="{{ route('password.reset') }}">
        <input type="hidden" name="token" value="{{ $token }}">

    {{csrf_field()}}
    {!! BootForm::email('email', 'Email', null,['placeholder' => 'example@mail.com', 'required']) !!}

    {!! BootForm::password('password', 'Password', ['required']) !!}

    {!! BootForm::password('password_confirmation', 'Confirm Password', ['required']) !!}


    {!! BootForm::button('Reset Password', ['class'=>'btn btn-primary btn-sm pull-right', 'type'=>'submit', 'name'=>'forgotpass']) !!}
    {{ BootForm::close() }}
@endsection