@php( $formTitle = 'Sign Up')

@extends('dashboard::layouts.login')

@section('link')
    <a href="/dashboard/login" class="signUp"><i class="fa fa-arrow-left" aria-hidden="true"></i> Sign In </a>
@endsection
@section('login-form')
    @parent
    {{ BootForm::open() }}

    {!! BootForm::text('name', 'Name', null,['placeholder' => 'Your Name', 'class'=>'log', 'required']) !!}
    {!! BootForm::text('email', 'Email', null,['placeholder' => 'example@mail.com', 'class'=>'log', 'required']) !!}
    {!! BootForm::password('password', 'Password', ['placeholder' => 'Your Password', 'class'=>'log', 'required']) !!}
    <br>
    {!! BootForm::button('Register', ['name'=>'register', 'type' => 'submit', 'class' => 'btn btn-primary btn-sm logB']) !!}

    {{ BootForm::close() }}

    @if(count(Session::get('affiliateId')))
        <p class="sponsor"> Your sponsor is {{Session::get('affiliateId')}}</p>
    @endif
@endsection