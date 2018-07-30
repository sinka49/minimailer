@extends('dashboard::layouts.dash')

@section('js')
    @parent
    <script>
        function toggleIcon(e) {
            $(e.target)
                    .prev('.panel-heading')
                    .find(".more-less")
                    .toggleClass('glyphicon-plus glyphicon-minus');
        }
        $('.panel-group').on('hidden.bs.collapse', toggleIcon);
        $('.panel-group').on('shown.bs.collapse', toggleIcon);

           function buyNow(th) {
                 var id =  th.getAttribute("data-product");
                 $("#product").val(id);
                 $('.stripe-button-el').click();
                return false;
            }

    </script>
@endsection

@section('page-content')
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        @foreach($products as $product)
            @include('dashboard::app.subscription-item', $product)
        @endforeach
    </div>
    @include('dashboard::app.stripe_form')

@endsection