@extends('dashboard::layouts.dash')

@section('page-content')
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4><i class="fa fa-fw fa-share"></i>Finance</h4>
            </div>

            <div class="panel-body">
                <div class="my_balance">
                    <h3>My Balance</h3>
                     <p>Total - ${{$totalAm}}</p>
                    <a class="payout btn btn-success btn-block" href="/dashboard/finance/payout" @if($totalAm<60 or !$access) disabled="disabled" onclick="return false;" @endif>Payout</a>
                    @if(Session::has('message'))<p class="w "> {{Session::get('message')}} </p>@endif
                </div>
                <h3>Finance operations</h3>
                <table class="table table-hover table-bordered">
                    <thead>
                    <tr>
                        <th class="col-md-1">#</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Amount</th>
                         <th>Date</th>
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
                        @foreach($transactions["data"] as $t)
                            <tr>
                                <td>{{$t["id"]}}</td>
                                <td>{{$t["description"]}}</td>
                                <td>Payment completed</td>
                                <td>${{$t["amount"]/100}}</td>
                                <td>{{date('Y-m-d H:i' ,$t["created"])}}</td>
                            </tr>
                        @endforeach
                    @endif

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection