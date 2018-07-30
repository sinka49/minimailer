@extends('dashboard::layouts.dash')

@section('page-content')
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4><i class="fa fa-fw fa-share"></i>{{$title}}</h4>
            </div>
            <div class="panel-body">
                <h4>This section is not available.</h4>
                <h4>Please purchase one of the subscriptions to be able to use all functions of The Mini-Mailer</h4>
                <a href="{{URL::to('dashboard/subscription')}}">See all subscriptions</a>
            </div>
        </div>
    </div>
@endsection