@extends('dashboard::layouts.dash')
@section('js')
    @parent
    {!! ViewHelper::usePageScript('dash.history') !!}
@endsection
@section('page-content')
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4><i class="fa fa-fw fa-share"></i>Mailing History</h4>
            </div>
            <div class="panel-body">
                <table class="table table-hover table-bordered mailing">
                    <thead>
                    <tr>
                        <th class="col-md-1">#</th>
                        <th>Smtp Account ID</th>
                        <th>Recipient</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if( empty($mailingHistory) || count($mailingHistory) == 0 )
                        <tr>
                            <td colspan="7">
                                <p class="text-center">No mails sent</p>
                            </td>
                        </tr>
                    @else
                        @foreach($mailingHistory as $m)
                            <tr>
                                <td>{{$m->id}}</td>
                                <td>{{$m->smtp_account_email}}</td>
                                <td>{{$m->recipient_email}}</td>
                                <td>{{$m->status}}</td>
                                <td>{{$m->date}}</td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
                {{ $mailingHistory->links() }}
                <div class="clear">

                    <button type="button" class="btn btn-warning btn-sm  clear_hist" data-user="{{Auth::user()->id}}">Clear History
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection