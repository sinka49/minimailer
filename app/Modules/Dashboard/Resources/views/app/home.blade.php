@extends('dashboard::layouts.dash')

@section('page-content')
    <p class="text-danger"></p>
    @if( !empty($homePage->title) )
        <h2>{{$homePage->title}}</h2>
    @endif
    <hr>

    {!! $homePage->video !!}

    @if( !empty($homePage->body) )
        {!! $homePage->body !!}
    @endif

    @if( !$signed )
        <a href="/dashboard/subscription" class="btn btn-primary" id="buy-btm" style="width: 250px;"
        >
            <h4><i class="glyphicon glyphicon-shopping-cart"> </i> BUY NOW!</h4>

        </a>
        @include('dashboard::layouts.stripe_form')
    @endif

@endsection