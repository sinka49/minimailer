<?$s = \App\Models\Main::find(2)?>
        <!DOCTYPE html>
<html>
<head>


    {!! $seo->headGoog !!}
    <meta charset="UTF-8">
    <title>The Mini-Mailer</title>
    <meta name="description"
          content="{{$seo->description}}">

    <meta name="keywords"
          content="{{$seo->keywords}}">
    <meta name="rating" content="{{$seo->rating}}">
    <meta name="robots" content="{{$seo->robots}}">
    <meta name="revisit-after" content="{{$seo->revisit}}">
    <link rel="icon" type="image/vnd.microsoft.icon" href="{{$seo->favicon}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @section('css')
        <link href="{{ URL::asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ URL::asset('css/modern-business.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ URL::asset('css/style.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ URL::asset('font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css"/>

        <style type="text/css" media="all">
            /* fix rtl for demo */
            .chosen-rtl .chosen-drop {
                left: -9000px;
            }

            .loader-image-div {
                position: absolute;
                display: none;
                z-index: 2000;
                width: 100%;
                height: 100%;
                text-align: center;
                background: rgba(255, 255, 255, .5);
            }

            .loader-image-div img {
                margin-top: 20%;
            }
        </style>
    @show
    @section('js')
        <script src="{{ URL::asset('js/jquery.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap.min.js') }}"></script>
        <script>
            var getToken = function () {
                return $('meta[name="csrf-token"]').attr('content');
            };
        </script>
    @show
</head>
<body>
    {!!$seo->bodyGoog!!}
    <div class="loader-image-div">
        <img src="/images/ajax-loader.gif">
    </div>
    <div style="margin-bottom: 50px;"></div>

    @include('backend::layouts.top-menu')

    <div class="container">
        @yield('content', 'content')
    </div>
</body>
</html>