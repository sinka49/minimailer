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
    {!! BootForm::open() !!}
        {!! BootForm::text('title', 'Title', $homePage->title ) !!}
        {!! BootForm::text('video', 'Embed player code', $homePage->video) !!}
        {!! BootForm::textarea('body', 'Text after video', $homePage->body,['id' => 'editor']) !!}
        {!! BootForm::button('Save',['type' => 'submit']) !!}
    {!! BootForm::close() !!}
@endsection