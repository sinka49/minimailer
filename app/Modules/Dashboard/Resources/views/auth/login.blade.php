@php( $formTitle = 'Log In')

@extends('dashboard::layouts.login')
@section('link')
    <a href="/dashboard/register" class="signUp">Sign Up Now <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
@endsection
@section('login-form')
    @parent
    {{ BootForm::open() }}
    {!! BootForm::text('email', 'Email', null,['placeholder' => 'example@mail.com', 'class'=>'log', 'required']) !!}
    {!! BootForm::password('password', 'Password', ['placeholder' => 'Your Password', 'class'=>'log', 'required']) !!}
    <div class="form-group">



        {!! Form::button('Login', ['type'=> 'submit', 'name'=>'login', 'class' => 'btn btn-primary btn-sm logB']) !!}<br>


    </div>
   <div class="forg">{!! HTML::link('dashboard/password/reset', 'Forgot password?') !!}</div>
    {{ BootForm::close() }}
@endsection