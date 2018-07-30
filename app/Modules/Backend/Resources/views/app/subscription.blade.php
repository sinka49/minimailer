@extends('backend::layouts.dash')

@section('page-content')
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <h4><i class="fa fa-fw fa-share"></i>Training
                    <a href="{{URL::to('/opera/subscription')}}" class="btn btn-success pull-right">New</a>
                </h4>
            </div>
            <div class="panel-body">
                <table class="table table-hover table-bordered">
                    <thead>
                    <tr>
                        <th class="col-md-1">ID</th>
                        <th class="col-md-7">Title</th>
                        <th class="col-md-7">Sort</th>
                        <th class="col-md-1">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if( empty($posts) || count($posts) == 0 )
                        <tr>
                            <td colspan="4">
                                <p class="text-center">No subscription found</p>
                            </td>
                        </tr>
                    @else
                        @foreach($posts as $p)
                            <tr>
                                <td>{{$p->id}}</td>
                                <td><a href="{{ URL::to('/opera/subscription', ['id' => $p->id]) }}">{{$p->title}}</a></td>
                                <th class="col-md-7">{{$p->sort}}</th>
                                <td>
                                   <a href="{{ URL::to('/opera/subscription/remove', ['id' => $p->id]) }}"><div class="fa fa-times-circle"></div></a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection