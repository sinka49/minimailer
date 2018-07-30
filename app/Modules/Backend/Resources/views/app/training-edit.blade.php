@extends('backend::layouts.dash')

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
                {!! BootForm::open() !!}
                    {!! BootForm::text('title', 'Title', $post->title) !!}
                    {!! BootForm::textarea('body', 'Body', $post->body, ['id' => 'editor']) !!}
                    {!! BootForm::text('video', 'Video Frame', $post->video) !!}
                    {!! BootForm::select('status', 'Status', \App\Models\TrainingPost::getStatuses(), $post->status ) !!}

                    <button type="submit" class="btn btn-primary pull-left">Save</button>
                    <a href="{{ URL::to('opera/trainings') }}" class="btn btn-danger pull-right">Cancel</a>

                {!! BootForm::close() !!}
            </div>
        </div>
    </div>
@endsection