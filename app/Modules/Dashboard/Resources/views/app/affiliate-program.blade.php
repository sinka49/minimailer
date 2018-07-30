<?
use App\Models\User;

?>
@extends('dashboard::layouts.dash')
@section('js')
    {!! ViewHelper::usePageScript('jquery') !!}
    {!! ViewHelper::usePageScript('aff') !!}
@endsection
@section('page-content')
    <form action="/dashboard/affiliate-program/mail" id="affiliate_send" method="POST">
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4><i class="fa fa-fw fa-share"></i>Affiliate Program</h4>
            </div>
            <div class="panel-body">
                 <label for="affiliate_name" class="control-label">My Referral Name</label>
                <div class="form-group ">
                    <div class="row">
                        <div class="col-md-10">
                            <input class="form-control" id="affiliate_name" name="affiliate_name" type="text"
                                   value="{{ $affiliateName  }}" disabled>
                        </div>

                    </div>
                </div>
                {!! BootForm::close() !!}

                {!! BootForm::text('affiliate_url','My Referral Link', $affiliateUrl ) !!}
                <a href="/dashboard/banners" class="btn btn-success btn-block" style="width:150px;">Banners</a> <br>

                <p>We pay on two levels. {{$level1}}%  first level. And {{$level2}}%   on the second level. You get rewarded for helping your affiliates refer others! <br>*Payouts are available for withdraw after a $60 balance has been built up. Please expect a 24-48 hour payout time after payout has been submitted in the Finance tab.</p>

                <div class="form-group ">
                    <label for="affiliate_url" class="control-label">My Referral's Stats</label>
                    <table class="table table-hover table-bordered">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Count Refferals</th>
                            <th>Profit from user</th>
                            <th>Profit from Refferals</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if( empty($affiliateUsers) || count($affiliateUsers) == 0 )
                            <tr>
                                <td colspan="8">
                                    <p class="text-center">No affiliated users found</p>
                                </td>
                            </tr>
                        @else
                            @foreach($affiliateUsers as $a)
                                <tr>
                                    <td><input type="checkbox" value="{{$a->email}}" name="email_addresses[]" class="emails"></td>
                                    <td>{{$a->name}}</td>
                                    <td>{{$a->email}}</td>
                                    <td>{{$a->status}}</td>
                                    <td>{{$a->total}}</td>
                                    <td>${{$a->totalAff}}</td>
                                    <td>${{$a->totalAffWithUsers}}</td>
                                    <td>${{$a->totalAffWithUsers+$a->totalAff}}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    <div class="form-group clear">
                        <input type="checkbox" id="
                        check_all" name="check_all" class="check_all"/>
                        <label for="check_all">
                            Check/Un-check All
                        </label>
                    </div>
                </div>
            </div>
        </div>



        <div class="panel panel-default">
            <div class="panel-body flex" style="box-shadow: none">
                <div class="col5" style="padding-right: 35px;">
                    <h4 style="margin-bottom: 35px;">SMTP accounts</h4>
                <table class="table table-hover table-bordered smtp-account" >
                    <thead>
                    <tr>
                        <th class="col-md-1">
                            Select
                        </th>
                        <th>ID</th>
                        <th>Account</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if( !empty($smtpAccounts) || count($smtpAccounts) )
                        @foreach($smtpAccounts as $account)
                            <tr>
                                <td class="text-center">
                                    <input name="accounts[]" class="smtp_checkbox" type="checkbox" value="{{ $account->id }}">
                                </td>
                                <td>{{ $account->id }}</td>
                                <td>
                                    {{ $account->email }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
                    <div class="form-group clear">
                        <input type="checkbox" id="smtp_check_all" name="check_all" class="smtp_check_all"/>
                        <label for="smtp_check_all">
                            Check/Un-check All
                        </label>
                    </div>
                </div>
                <div class="col5">
                    <h4>Message</h4>
                    <input type="hidden" name="from_email" value="{{Auth::user()->email}}">
                    <input type="hidden" name="from_name" value="{{Auth::user()->name}}">
                    {{csrf_field()}}
                    {!! BootForm::text('subject', 'Subject','',['class'=>'form-control subject', 'placeholder' => 'Enter Subject']) !!}
                    {!! BootForm::textarea('body', 'Body','',['class'=>'form-control body', 'rows'=>'10', 'name'=>'body', 'id'=>'editor', 'placeholder'=>'Enter Email Body']) !!}
                    {!! BootForm::button('Send Message',['type' => 'button', 'class' => 'btn btn-success btn-block send_message']) !!}

                </div>
            </div>
        </div>
        <div class="col-md-12 logs_container" style="padding: 0;">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4><i class="fa fa-fw fa-share"></i> Log</h4>
                </div>
                <div class="panel-body">
                    <table class="table table-hover table-bordered log_table" style="display:none">
                        <thead>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Message</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </form>

@endsection