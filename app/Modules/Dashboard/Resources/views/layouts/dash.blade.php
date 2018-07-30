@extends('dashboard::layouts.base')

        @section('content')
            <header class="dashheader flexContainer">
                <div class="logo_wrap"><img width="250px" class="logo-image" src="/images/Design-png.png"></div>

                <div class="topnab"><a href="/dashboard/profile"><i class="fa fa-user" aria-hidden="true"></i>&nbsp; &nbsp;Profile</a><a href="/dashboard/logout"><i class="fa fa-sign-out" aria-hidden="true"></i>&nbsp;&nbsp;Logout</a></div>
            </header>
            <section class="flexContainer">
                <section class="navLeftSide">
                    @include('dashboard::layouts.dash-menu')
                </section>
                <section class="mainArea">
                    <div class="containerDash">



                         @yield('page-content')


                    </div>
                </section>
            </section>
        @endsection
