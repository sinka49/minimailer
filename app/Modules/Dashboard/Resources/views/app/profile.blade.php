@extends('dashboard::layouts.dash')

@section('page-content')
    <script src="/js/jquery.creditCardValidator.js"></script>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4><i class="fa fa-fw fa-share"></i>Profile settings</h4>
            </div>
            <div class="panel-body">

                           <div class="flex" style="box-shadow: none">
                                <div class="col6">
                                    <h4>Personal data</h4>
                                    <p><span>Email:</span>{{Auth::user()->email}}</p>
                                    <p><span>Name:</span> {{Auth::user()->name}}</p>


                                </div>
                                <div class="col4 ">
                                    <h4>Finance data</h4>
                                    <p><span>PayPal email:</span> @if(count($pp)) {{$pp->email_paypal}}@endif</p>

                                    @if(!empty($ref))<p><span>Referral Link:</span> {{$ref}}</p>@endif

                                </div>

                            </div>
                @if(!count($pp))  <p class="war">
                    Please, enter your information for payout.
                </p>
                <p class="w">

                    This is necessary to transfer funds to you from the affiliate program. Please place  your Paypal email below.

                </p>
            @endif
                <div class="flex" style="box-shadow: none">
                    <div class="col5">
                        <h4>Change name</h4>
                        <form action="/dashboard/profile/update/name" method="POST">
                            {{csrf_field()}}
                            <input type="text" class="form-control form-control subject ss" name="name">
                            <button type="submit" class="btn btn-success" >Save</button>
                        </form>
                        <h4>Change password</h4>
                        <form action="/dashboard/profile/update/password" method="POST">
                            {{csrf_field()}}
                            <input type="text" class="form-control form-control subject ss" name="password">
                            <button type="submit" class="btn btn-success" >Save</button>
                        </form>

                    </div>
                    <div class="col3 ">
                        @if(count($pp))
                            @if($pp->email_paypal)
                            <h4>Change PayPal email</h4>
                            <form action="/dashboard/profile/update/ppemail" method="POST">
                                {{csrf_field()}}
                                <input type="text" class="form-control form-control subject ss" name="email_paypal">
                                <button type="submit" class="btn btn-success" >Save</button>
                            </form>
                            @else
                                <h4>Add PayPal email</h4>
                                <form action="/dashboard/profile/update/ppemail" method="POST">
                                    {{csrf_field()}}
                                    <input type="text" class="form-control form-control subject ss"  name="email_paypal">
                                    <button type="submit" class="btn btn-success" >Save</button>
                                </form>
                            @endif

                        @else

                            <h4>Add PayPal email</h4>
                            <form action="/dashboard/profile/update/ppemail" method="POST">
                                {{csrf_field()}}
                                <input type="text" class="form-control form-control subject ss"  name="email_paypal">
                                <button type="submit" class="btn btn-success" >Save</button>
                            </form>

                       @endif
                    </div>

                </div>


            </div>
        </div>
    </div>
    <script src="/js/payment.js"></script>
@endsection