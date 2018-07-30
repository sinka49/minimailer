@extends('backend::layouts.dash')

@section('js')
    @parent
    {!! ViewHelper::useCkeditor() !!}


@endsection

@section('page-content')
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4><i class="fa fa-fw fa-share"></i>SEO</h4>
            </div>
            <div class="panel-body">
                <form action="/opera/seo" method="POST" enctype="multipart/form-data">
                    {{csrf_field()}}
                        <div class="row">
                            <div class="block meta">
                                <label> Title</label>
                                <input type="text"  name="title" class="form-control email" @if(isset($page->title)) value='{{$page->title}}' @endif  placeholder="Enter meta title"/>
                            </div>


                            <div class="block meta">
                                <label> Keywords</label>
                                <textarea name="keywords" id="" cols="90" rows="5">@if(isset($page->keywords)) {{$page->keywords}} @endif </textarea>
                            </div>
                            <div class="block meta">
                                <label> Description</label>
                                <textarea name="description" id="" cols="90" rows="5">@if(isset($page->description)) {{$page->description}} @endif</textarea>
                            </div>

                            <div class="block meta">
                                <label> Rating</label>
                                <input type="text"  name="rating" class="form-control email" @if(isset($page->rating)) value='{{$page->rating}}'  @endif placeholder="Enter meta rating"/>

                            </div>
                            <div class="block meta">
                                <label> Robots</label>
                                <input type="text"  name="robots" class="form-control email" @if(isset($page->robots)) value='{{$page->robots}}' @endif placeholder="Enter meta robots"/>

                            </div>
                            <div class="block meta">
                                <label> Revisit-after</label>
                                <input type="text"  name="revisit" class="form-control email" @if(isset($page->revisit)) value='{{$page->revisit}}' @endif placeholder="Enter meta revisit-after"/>

                            </div>
                            <div class="block meta">
                                <label> Head Google Tag</label>
                                <input type="text"  name="headGoog" class="form-control email" @if(isset($page->headGoog)) value='{{$page->headGoog}}' @endif placeholder="Enter head google tag manager"/>

                            </div>
                            <div class="block meta">
                                <label> Body Google Tag</label>
                                <input type="text"  name="bodyGoog" class="form-control email" @if(isset($page->bodyGoog)) value='{{$page->bodyGoog}}' @endif placeholder="Enter body google tag manager"/>

                            </div>
                            <div class="block meta">
                                <label> Favicon</label>
                                <input type="file" class="file" name="fav">
                                <img src="{{$page->favicon}}" style="height: 20px; width:20px !important;" alt="">
                            </div>
                        </div>

                            <button type="submit" class="btn btn-primary btn-sm pull-right" style="width:200px; padding: 10px;
                             font-size:20px; margin: 10px;">Save</button>


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