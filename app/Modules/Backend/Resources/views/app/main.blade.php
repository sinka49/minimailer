@extends('backend::layouts.dash')

@section('js')
    @parent
    {!! ViewHelper::useCkeditor() !!}


@endsection

@section('page-content')
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4><i class="fa fa-fw fa-share"></i>Main page</h4>
            </div>
            <div class="panel-body">
                <form action="/opera/main" method="POST" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div >
                        <div class="row">
                            <div class="block">
                                <input type="checkbox" name="videoBlock" @if($page->video->visible) checked @endif>
                            <div>
                                <input type="text" name="video" class="form-control email" value='{{$page->video->video}}' placeholder="Enter frame"/>
                            </div>

                            </div>
                            <div class="block">
                                <input type="checkbox" name="block1" @if($page->item1->visible) checked @endif>
                            <div>
                                <input type="text" name="title1" class="form-control password"
                                       value="{{$page->item1->title}}"/>
                            </div>
                            <br/>
                            <br/>
                             <div>
                                <textarea name="area1" id="" cols="90" rows="5">{{$page->item1->text}}</textarea>
                            </div>
                            <br/>
                            <br/>
                            </div>
                            <div class="block">
                                <input type="checkbox" name="block2" @if($page->item2->visible)checked @endif>
                            <div>
                                <input type="text" name="title2" class="form-control password"
                                       value="{{$page->item2->title}}"/>
                            </div>
                            <br/>
                            <br/>

                            <div >
                                <textarea name="area2" id="" cols="90" rows="5">{{$page->item2->text}}</textarea>
                            </div>
                            <br/>
                            <br/>
                            </div>
                            <div class="block">
                                <input type="checkbox" name="block3" @if($page->item3->visible) checked @endif>
                            <div>
                                <input type="text" name="title3" class="form-control password"
                                       value="{{$page->item3->title}}"/>
                            </div>
                            <br/>
                            <br/>
                            <div >
                                <textarea name="area3" id="" cols="90" rows="5">{{$page->item3->text}}</textarea>
                            </div>
                            <br/>
                            <br/>
                            </div>
                            <div class="block">
                                <input type="checkbox" name="block4" @if(strlen($page->item4->visible))checked @endif>
                                <div >
                                    <input type="text" name="title4" class="form-control password"
                                           value="{{$page->item4->title}}"/>
                                </div>
                                <br/>
                                <br/>
                                <div >
                                    <textarea name="area4" id="" cols="90" rows="5">{{$page->item4->text}}</textarea>
                                </div>
                                <input type="file" name="file4" class="file">
                                <img src="{{$page->item4->src}}" alt="">
                                <br/>
                                <br/>
                            </div>
                            <div class="block">
                                <input type="checkbox" name="block5" @if($page->item5->visible) checked @endif>
                                <div >
                                    <input type="text" name="title5" class="form-control password"
                                           value="{{$page->item5->title}}"/>
                                </div>
                                <br/>
                                <br/>
                                <div >
                                    <textarea name="area5" id="" cols="90" rows="5">{{$page->item5->text}}</textarea>
                                </div>
                                <input type="file" name="file5" class="file">
                                <img src="{{$page->item5->src}}" alt="">
                                <br/>
                                <br/>
                            </div>

                            <div class="block">
                                <input type="checkbox" name="block6" @if($page->item6->visible) checked @endif>
                                <div>
                                    <input type="text" name="title6" class="form-control password"
                                           value="{{$page->item6->title}}"/>
                                </div>
                                <br/>
                                <br/>
                                <div>
                                    <textarea name="area6" id="" cols="90" rows="5">{{$page->item6->text}}</textarea>
                                </div>
                                <input type="file" name="file6" class="file">
                                <img src="{{$page->item6->src}}" alt="">
                                <br/>
                                <br/>
                            </div>

                            <div class="block">
                                <input type="checkbox" name="block7" @if($page->item7->visible) checked @endif>
                                <div >
                                    <input type="text" name="title7" class="form-control password"
                                           value="{{$page->item7->title}}"/>
                                </div>
                                <br/>
                                <br/>
                                <div >
                                    <textarea name="area7" id="" cols="90" rows="5">{{$page->item7->text}}</textarea>
                                </div>
                                <br><br>
                                <div >
                                    <input type="text" name="title71" class="form-control password"
                                           value="{{$page->item7->title2}}"/>
                                </div>
                                <br/>
                                <br/>
                                <div>
                                    <textarea name="area71" id="" cols="90" rows="5">{{$page->item7->text2}}</textarea>

                                </div>
                                <input type="file" name="file7" class="file">
                                <img src="{{$page->item7->src}}" alt="">
                                <br/>
                                <br/>
                            </div>
                            <div class="block">
                                <input type="checkbox" name="block8" @if($page->item8->visible) checked @endif>
                                <div>
                                    <input type="text" name="title8" class="form-control password"
                                           value="{{$page->item8->title}}"/>
                                </div>
                                <br/>
                                <br/>
                                <div >
                                    <textarea name="area8" id="" cols="90" rows="5">{{$page->item8->text}}</textarea>
                                </div>
                                <input type="file" name="file8" class="file">
                                <img src="{{$page->item8->src}}" alt="">
                                <br/>
                                <br/>
                            </div>
                            <div class="block">
                                <input type="checkbox" name="block9" @if($page->item9->visible) checked @endif>
                                <div >
                                    <input type="text" name="title9" class="form-control password"
                                           value="{{$page->item9->title}}"/>
                                </div>
                                <br/>
                                <br/>
                                <div >
                                    <textarea name="area9" id="" cols="90" rows="5">{{$page->item9->text}}</textarea>
                                </div>

                                <br/>
                                <br/>
                            </div>
                            <div class="block">
                                <input type="checkbox" name="block10" @if($page->item10->visible) checked @endif>
                                <div >
                                    <input type="text" name="title10" class="form-control password"
                                           value="{{$page->item10->title}}"/>
                                </div>
                                <br/>
                                <br/>
                                <div >
                                    <textarea name="area10" id="" cols="90" rows="5">{{$page->item10->text}}</textarea>
                                </div>
                                <input type="file" name="file10" class="file">
                                <img src="{{$page->item10->src}}" alt="">
                                <br/>
                                <br/>
                            </div>
                            <div class="block">
                                <input type="checkbox" name="block11" @if($page->item11->visible) checked @endif>
                                <div >
                                    <input type="text" name="title11" class="form-control password"
                                           value="{{$page->item11->title}}"/>
                                </div>
                                <br/>
                                <br/>
                                <div>
                                    <textarea name="area11" id="" cols="90" rows="5">{{$page->item11->text}}</textarea>
                                </div>
                                <input type="file" name="file11" class="file">
                                <img src="{{$page->item11->src}}" alt="">
                                <br/>
                                <br/>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm pull-right" style="width:200px; padding: 10px;
                             font-size:20px; margin: 10px;">Save</button>
                        </div>
                    </div>
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