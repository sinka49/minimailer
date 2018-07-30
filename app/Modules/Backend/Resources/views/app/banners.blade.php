@extends('backend::layouts.dash')

@section('js')
    @parent
    {!! ViewHelper::useCkeditor() !!}


@endsection

@section('page-content')
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4><i class="fa fa-fw fa-share"></i>Banners</h4>
            </div>
            <div class="panel-body">


                        <div class="row">
                            @foreach($items as $item)
                            <div class="block banners">

                                <img src="{{$item->src}}" style="height: 250px; width:300px !important;" alt="">
                                <a href="/opera/banners/remove/{{$item->id}}" class="btn btn-warning btn-sm pull-right" style="width:100px; padding: 5px;
                             font-size:14px; margin: 10px;">Remove</a>
                            </div>
                            @endforeach
                        </div>
                    <hr>
                <form action="/opera/banners" method="POST" enctype="multipart/form-data">
                    <h4><i class="fa fa-fw fa-share"></i>New Banner</h4><br>
                    <input type="file" class="file" name="fav">
                    {{csrf_field()}}
                            <button type="submit" class="btn btn-primary btn-sm pull-right" style="width:100px; padding: 5px;
                             font-size:20px; margin: 10px;">Add</button>


                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $(".file").on("change",function () {
                $(this).siblings('img').remove();
            })

        })
    </script>
@endsection