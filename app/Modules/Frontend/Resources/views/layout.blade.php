<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/css/modern-business.css" rel="stylesheet" type="text/css"/>
    <link href="/css/animate.min.css" rel="stylesheet">
    <link href="/css/owl.transitions.css" rel="stylesheet">
    <link href="/css/prettyPhoto.css" rel="stylesheet">
    <link href="/css/main.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
    <link href="/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
        <!--[if lt IE 9]>
    <script src="/js/html5shiv.js"></script>
    <script src="/js/respond.min.js"></script>
    <![endif]--> 
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
        .btn-yello-top {
              border-radius: 15px;  box-shadow: 0 4px #999; }
        .btn-yello-top:hover {background-color: #937507}

        .btn-yello-top:active {
            background-image: linear-gradient(to bottom, rgb(254, 190, 0) 0%, #ce9b04 100%);
            box-shadow: 0 5px #666;
            transform: translateY(4px);
        }
        #buy-btm {
              border-radius: 10px;  box-shadow: 0 3px #999; }
        #buy-btm:active {
            box-shadow: 0 5px #666;
            transform: translateY(4px);
        }
    </style>
    <script src="/js/jquery.js"></script>
    <script src="/js/bootstrap.min.js"></script>

    <script src="https://maps.google.com/maps/api/js?sensor=true"></script>
    <script src="/js/mousescroll.js"></script>
    <script src="/js/smoothscroll.js"></script>
    <script src="/js/jquery.prettyPhoto.js"></script>
    <script src="/js/jquery.isotope.min.js"></script>
    <script src="/js/jquery.inview.min.js"></script>
    <script src="/js/wow.min.js"></script>
    <script src="/js/main.js"></script>
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

</head>
<body id="home" class="homepage" style="background-color: #fff">
    @yield('content')

