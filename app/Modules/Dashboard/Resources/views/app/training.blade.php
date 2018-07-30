@extends('dashboard::layouts.dash')

@section('page-content')
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4><i class="fa fa-fw fa-share"></i>Trainings</h4>
            </div>
            <div class="panel-body">
                    @if( empty($posts) || count($posts) == 0 )

                                <p class="text-center">No trainings found</p>

                    @else
                        @foreach($posts as $post)
                            <div class="training flex">
                                <div class="col6">
                                    <h3>{{$post->title}}</h3>
                                        {!!  $post->video!!}
                                </div>
                                <div class="col4 p">
                                    <div class="post">{!! $post->body !!}</div>
                                    <p>Published: {{date("d/m/y",strtotime($post->created_at))}}</p>
                                </div>

                            </div>
                        @endforeach
                    @endif

            </div>
        </div>
    </div>
@endsection