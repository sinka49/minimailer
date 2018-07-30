@extends('dashboard::layouts.dash')

@section('page-content')
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4><i class="fa fa-fw fa-share"></i>SMTP Accounts</h4>
            </div>
            <div class="panel-body">

                {!! BootForm::horizontal() !!}
                @if( $smtpAccount->id )
                    {!! BootForm::text('id', 'ID', $smtpAccount->id, [ 'class'=>'form-control from_name', 'readonly'=>'readonly', 'required' => 'required']) !!}
                @endif

                {!! BootForm::email('email', 'Email', $smtpAccount->email, [ 'class'=>'form-control from_name', 'placeholder' => 'my-name@google.com', 'required' => 'required']) !!}
                {!! BootForm::password('password', 'Password', [ 'class'=>'form-control from_name', 'required' => 'required']) !!}
                {!! BootForm::text('host', 'Smtp Host', $smtpAccount->host, [ 'class'=>'form-control from_name', 'placeholder' => 'smtp.gmail.com']) !!}
                {!! BootForm::number('port', 'Smtp Port', $smtpAccount->port, [ 'class'=>'form-control from_name', 'placeholder' => '587']) !!}
                {!! BootForm::text('ssl_enabled', 'SSL Enabled', $smtpAccount->ssl_enabled, [ 'class'=>'form-control from_name', 'placeholder' => 'tls']) !!}
                {!! BootForm::number('period', 'Sending period(min)', $smtpAccount->period, [ 'class'=>'form-control from_name', 'placeholder' => '3600']) !!}


                <div class="form-group">
                    <label for="smtp_account_enabled" class="col-sm-3 control-label">Active</label>
                    <div class="col-md-9">
                        <input name="enabled" value="1" required type="checkbox" class="form-control"
                               id="smtp_account_enabled" checked="{{ $smtpAccount->enabled ? 'enabled' : '' }}">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="{!! URL::to('/dashboard/smtp-accounts') !!}" class="btn btn-default">Cancel</a>
                    </div>
                </div>
                {!! BootForm::close() !!}
            </div>
        </div>
    </div>
@endsection