@extends('dashboard::layouts.dash')

@section('page-content')
    <script src="/js/jquery.creditCardValidator.js"></script>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4><i class="fa fa-fw fa-share"></i>Profile settings</h4>
            </div>
            <div class="panel-body">



                @if(Session::has('message'))<p class="message"> {{Session::get('message')}} </p>@endif
                <div style="width:70%; margin: 0 auto;">
                    <form action="/dashboard/finance/sendPayout" method="POST">
                        {{csrf_field()}}
                        {!! BootForm::text('from_email', 'From (Email)', Auth::user()->email, [ 'class'=>'form-control from_email', "disabled"]) !!}
                        {!! BootForm::text('paypal', 'PayPal Email', $pp->email_paypal, [ 'class'=>'form-control from_name', "disabled"]) !!}
                        {!! BootForm::number('amount', 'Amount ($)', $totalAmNow,['class'=>'form-control subject', "min"=>60, "max"=>$totalAmNow]) !!}

                        {!! BootForm::button('Payout',['type' => 'submit', 'class' => 'btn btn-success btn-block send_message']) !!}
                    </form>

                </div>


            </div>
        </div>
    </div>
    <script src="/js/payment.js"></script>
@endsection