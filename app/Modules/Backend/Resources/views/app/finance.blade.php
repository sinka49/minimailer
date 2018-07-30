@extends('backend::layouts.dash')

@section('page-content')
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4><i class="fa fa-fw fa-share"></i>Finance</h4>
            </div>
            <div class="panel-body">

                <table class="table table-hover table-bordered">
                    <thead>
                    <tr>
                        <th class="col-md-1">#</th>
                        <th>User</th>
                        <th>PayPal email</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if( empty($transactions) || count($transactions) == 0 )
                        <tr>
                            <td colspan="7">
                                <p class="text-center">No transactions found</p>
                            </td>
                        </tr>
                    @else
                        @foreach($transactions as $t)
                            <tr @if($t->status == 0) class="green" @endif>
                                <td>{{$t->id}}</td>
                                <td>{{ $t->email}}</td>
                                <td>{{ $t->paypal}}</td>
                                <td>{{$t->stat}}</td>
                                <td>${{$t->amount}}</td>
                                <td>{{date('m/d/Y H:i:s', strtotime($t->created_at))}}</td>
                                <td>@if($t->status == 0)  <a href="/opera/finance/paid/{{$t->id}}" class="btn btn-success">Paid</a>@endif</td>
                            </tr>
                        @endforeach
                    @endif

                    </tbody>
                </table>
                {{ $transactions->links() }}
                <a href="/opera/finance/remove" class="btn btn-danger">Remove Paid Payouts</a>

            </div>
        </div>
    </div>
@endsection