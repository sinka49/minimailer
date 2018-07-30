@extends('dashboard::layouts.dash')

@section('page-content')
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4><i class="fa fa-fw fa-share"></i>Support</h4>
            </div>
            <p class="panel-body">

                          <h3 style="padding-left: 20px; text-align: center">Leave your message</h3>
                @if(Session::has('message'))<p class="message"> {{Session::get('message')}} </p>@endif
                        <div style="width:70%; margin: 0 auto;">
                        <form action="/dashboard/support" method="POST">
                            {{csrf_field()}}
                        {!! BootForm::text('from_email', 'From (Email)', Auth::user()->email, [ 'class'=>'form-control from_email']) !!}
                        {!! BootForm::text('from_name', 'From (Name)', Auth::user()->name, [ 'class'=>'form-control from_name']) !!}
                        {!! BootForm::text('subject', 'Subject','',['class'=>'form-control subject', 'placeholder' => 'Enter Subject']) !!}
                        {!! BootForm::textarea('body', 'Body','',['class'=>'form-control body', 'rows'=>'10', 'name'=>'body', 'id'=>'editor', 'placeholder'=>'Enter Email Body']) !!}
                        {!! BootForm::button('Send Message',['type' => 'submit', 'class' => 'btn btn-success btn-block send_message']) !!}
                        </form>

                        </div>

            <br><br>

            </div>
        </div>
    </div>

@endsection