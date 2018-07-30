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
                    {!! BootForm::textarea('description', 'Description', $post->description, ['id' => 'editor']) !!}
                    {!! BootForm::number('initial_price', 'Initial price', $post->initial_price) !!}
                    {!! BootForm::number('renew_price', 'Renew price', $post->renew_price) !!}
                    {!! BootForm::number('renew_period', 'Renew period', $post->renew_period) !!}
                    {!! BootForm::number('sort', 'Sort', $post->sort) !!}
                    <button type="submit" class="btn btn-primary pull-left">Save</button>
                    <a href="{{ URL::to('opera/subscriptions') }}" class="btn btn-danger pull-right">Cancel</a>

                {!! BootForm::close() !!}
            </div>
        </div>
    </div>
@endsection