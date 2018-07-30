@extends('dashboard::layouts.dash')

@section('js')
    @parent
    {!! ViewHelper::useCkeditor() !!}

    <script>
        $(function(){
            editor = CKEDITOR.replace('editor');
        });
    </script>
@endsection

@section('page-content')
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4><i class="fa fa-fw fa-share"></i>Training</h4>
            </div>
            <div class="panel-body">
                <h3>{{$post->title}}</h3>
                <div class="post">{!! $post->body !!}</div>
            </div>
        </div>
    </div>
@endsection