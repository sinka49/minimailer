@extends('dashboard::layouts.dash')

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

                                <img src="{{$item->src}}" style="height: 250px; width:300px !important;">
                                <input type="text" class="form-control" value='<a href="https://theminimailer.com?affiliateId={{$user->affiliate_name}}"><img src="{{$item->src}}" height="250" width="300"></a>'>
                                  </div>
                            @endforeach
                            
                        </div>


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