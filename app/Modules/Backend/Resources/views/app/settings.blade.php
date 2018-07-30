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
                <h4><i class="fa fa-fw fa-share"></i>Affilate settings</h4>
            </div>
            <div class="panel-body">
                {!! BootForm::open() !!}
                    {!! BootForm::number('level1', 'Percent Affilate Level 1',$setting->level1,["min"=>"0", "max"=>"100"]) !!}
                    {!! BootForm::number('level2', 'Percent Affilate Level 2',$setting->level2,["min"=>"0", "max"=>"100"]) !!}
                    <button type="submit" class="btn btn-primary pull-left">Save changes</button>
                {!! BootForm::close() !!}
            </div>
        </div>
    </div>
@endsection