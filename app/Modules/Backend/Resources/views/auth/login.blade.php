@php( $formTitle = 'Login Form')

@extends('backend::layouts.login')

@section('login-form')
    @parent
    {{ BootForm::open() }}
    {!! BootForm::text('email', 'Email', null,['placeholder' => 'example@mail.com', 'required']) !!}
    {!! BootForm::password('password', 'Password', ['placeholder' => 'Your Password', 'required']) !!}

    <div class="form-group">
        <span>Forgot password? </span>{!! HTML::link('opera/password/email', 'Click here') !!}
        {!! Form::button('Login', ['type'=> 'submit', 'name'=>'login', 'class' => 'btn btn-primary btn-sm pull-right']) !!}
    </div>
    {{ BootForm::close() }}
@endsection