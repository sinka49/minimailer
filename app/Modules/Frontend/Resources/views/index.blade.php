@extends('frontend::layout')

@section('content')
<header id="header">
    <nav id="main-menu" class="navbar navbar-default navbar-fixed-top" role="banner">
<div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.php"><img src="/images/Design-mini.png" alt="logo"></a>
                </div>
                <div class="collapse navbar-collapse navbar-right">
                    <ul class="nav navbar-nav">
                        <li class="scroll active"><a href="#home">Home</a></li>
                        <li class="scroll"><a href="#work-process">About</a></li>
                        <li class="scroll"><a href="<?= url( 'dashboard/login' ) ?>">Sign In</a></li>

     
                    </ul>
                </div>
            </div><!--/.container-->
        </nav><!--/nav-->
    </header><!--/header-->
    <div class="row text-center" style="margin-top: 70px; width: 97%; text-align: center;">

    <div class="col-md-11 " id="vid" style="margin-left: 8%; margin-top: -1.2%;">
    <style type="text/css">
    	@media all and (min-width: 1224px)
    	{
    		#vid {  width: 740px; height: 440px; margin: auto; float: none; display: inline-block; }

    	}
    </style>

            <div class="embed-responsive embed-responsive-16by9">
               @if($page->video->visible){!!$page->video->video!!}@endif
               <!--video controls preload="false">
                    <source src="https://youtu.be/278bDIC026I" type="video/mp4">
                    <source src="https://youtu.be/278bDIC026I" type="video/ogg">
                    Your browser does not support the video tag.
                    </video-->
            </div>
        <div class="row"><br><br></div>
        <div class="col-md-8 col-md-offset-3" style="margin-bottom: 20px; margin: auto; float: none; display: inline-block;">
            <a href="/dashboard/register" id="buy-l"  class=" btn-yello-top">
                <h4><i class="glyphicon glyphicon-shopping-cart"> </i>SIGN UP NOW</h4>
                <span><strong>Click Here to sign up the Mini-Mailer!</strong></span>
            </a>
        </div>
    </div>

    </div>
@if($page->item1->visible)
    <section id="cta" class="wow fadeIn">
        <div class="container">
            <div class="row">
                <div class="col-sm-11">
                    <h2>{{$page->item1->title}}</h2>
                    <p>{{$page->item1->text}}</p>
                </div>
            </div>
        </div>
    </section><!--/#cta-->
@endif
@if($page->item2->visible)
    <section id="work-process" style=" background-image: url(/images/bg_v2.jpg);">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title text-center wow fadeInDown">{{$page->item2->title}}</h2>
                <p class="text-center wow fadeInDown">{{$page->item2->text}}<i class="fa fa-smile-o"></i></p>
            </div>
        </div>
    </section><!--/#gladia-->
@endif

    <section id="about">
        <div class="container">
            @if($page->item3->visible)
            <div class="section-header">
                <h2 class="section-title text-center wow fadeInDown">{{$page->item3->title}}</h2>
                <p class="text-center wow fadeInDown">{{$page->item3->text}}</p>
            </div>
            @endif

            <div class="row">
                @if($page->item4->visible)
                <div class="col-sm-4 wow fadeInLeft animated" style="visibility: visible; animation-name: fadeInLeft;">
                    <img class="img-responsive" src="{{$page->item4->src}}" alt="">
                </div>
                <div class="col-sm-8 wow fadeInRight">
                    <h3 class="column-title">{{$page->item4->title}}</h3>
                    <p>{{$page->item4->text}}</p>
                </div>
                @endif
                @if($page->item5->visible)
                <div class="clearfix" style="margin-bottom:50px;"></div>
                <div class="col-sm-4 wow fadeInLeft animated" style="visibility: visible; animation-name: fadeInLeft;">
                    <img class="img-responsive" src="{{$page->item5->src}}" alt="">
                </div>
                <div class="col-sm-8 wow fadeInRight">
                    <h3 class="column-title">{{$page->item5->title}}</h3>
                    <p>{{$page->item5->text}} <span class="glyphicon glyphicon-thumbs-up"></span></p>
                </div>
                    @endif
                    @if($page->item6->visible)
                <div class="clearfix" style="margin-bottom:60px;"></div>
                <div class="col-sm-4 wow fadeInLeft animated" style="visibility: visible; animation-name: fadeInLeft;">
                    <img class="img-responsive" src="{{$page->item6->src}}" alt="">
                </div>
                <div class="col-sm-8 wow fadeInRight">    
                    <h3 class="column-title">{{$page->item6->title}}</h3>
                    <p>{{$page->item6->text}}</p>
                </div>
                    @endif
                    @if($page->item7->visible)
                <div class="clearfix" style="margin-bottom:70px;"></div>
                <div class="col-sm-4 wow fadeInLeft animated" style="visibility: visible; animation-name: fadeInLeft;">
                    <img class="img-responsive" src="{{$page->item7->src}}" alt="">
                </div>
                <div class="col-sm-8 wow fadeInRight">
                    <h3 class="column-title">{{$page->item7->title}}</h3>
                    <p>{{$page->item7->text}}</p>
                    <h3 class="column-title">{{$page->item7->title2}}</h3>
                    <p>{{$page->item7->text2}}</p>
                </div>
                </div>
                @endif
                @if($page->item8->visible)
                <div class="col-sm-4 wow fadeInLeft animated" style="visibility: visible; animation-name: fadeInLeft;">
                    <img class="img-responsive" src="{{$page->item8->src}}" alt="">
                </div>

                <div class="col-sm-8 wow fadeInRight">
                    <h3 class="column-title">{{$page->item8->title}}</h3>
                    <p>{{$page->item8->text}}</p>
                </div>
                <div class="clearfix" style="margin-bottom:50px;"></div>
                @endif

            </div>
        </div>
    </section><!--/#about-->
@if($page->item9->visible)
    <section id="get-in-touch">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title text-center wow fadeInDown">{{$page->item9->title}}</h2>
                <p class="text-center wow fadeInDown">{{$page->item9->text}}</p>
            </div>
        </div>
    </section><!--/#get-in-touch-->
@endif
    <section id="about2" >
        <div class="container">

            <div class="row">
                @if($page->item10->visible)
                <div class="col-sm-4 wow fadeInLeft animated" style="visibility: visible; animation-name: fadeInLeft;">
                    <img class="img-responsive" src="{{$page->item10->src}}" alt="">
                </div>
                <div class="col-sm-8 wow fadeInRight">
                    <h3 class="column-title">{{$page->item10->title}}</h3>
                    <p>{{$page->item10->text}}<span class="glyphicon glyphicon-ok"></span></p>
                </div>
                <div class="clearfix" style="margin-bottom:50px;"></div>
                @endif
                @if($page->item11->visible)
                <div class="col-sm-4 wow fadeInLeft animated" style="visibility: visible; animation-name: fadeInLeft;">
                    <img class="img-responsive" src="{{$page->item11->src}}" alt="">
                </div>
                <div class="col-sm-8 wow fadeInRight">
                    <h3 class="column-title">{{$page->item11->title}}</h3>
                    <p>{{$page->item11->text}} <i class="fa fa-magic"> </i> </p>
                </div>
                <div class="clearfix" style="margin-bottom:60px;"></div>
                    @endif

            </div>
        </div>
    </section>
            <div class="row" style="width: 97%;">
                <div class="col-md-12">
                <div class="col-md-12 signle-section-main" style="margin-top: 0">
                <div class="col-md-12" style="text-align: center; ">
                    <a href="/dashboard/register/" class="btn btn-primary" id="buy-btm" style="width: 250px;"
                      >
                        <h4><i class="glyphicon glyphicon-shopping-cart"> </i> SIGN UP NOW!</h4>
                    </a>

                </div>
            </div>
            </div>
            </div>

    <footer id="footer" style="margin-bottom: 0; padding-bottom: 0;">
        <div class="container" >
            <div class="row">
                <div class="col-sm-12 text-center">
                    &copy; 2017 Minimailer
                    <br/><br />
                    <img class="img-responsive" src="/images/Design-mini.png" style="display:inline">
                </div>              
            </div>
        </div>
    </footer><!--/#footer-->

<div class="Overlay-Background"></div>
</body>
</html>


@endsection