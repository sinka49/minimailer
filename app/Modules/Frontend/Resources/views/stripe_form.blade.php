<div id="order" style="display: none;">

        <header class="Header" role="banner">
            <div>
                <span></span>
                <span class="Header-navClose" aria-label="Close"></span>
            </div>
            <div class="Header-logo">
                <div class="Header-logoWrap">
                    <div class="Header-logoBevel"></div>
                    <div class="Header-logoBorder"></div><img class="Header-logoImageCatchError" src="/images/Design-mini.png">
                    <div class="Header-logoImage" alt="Logo" style="background-image: url('/images/Design-mini.png');"></div>
                </div>
            </div>
            <h1 class="Header-companyName u-textTruncate">The Mini-Mailer</h1>
            <h2 class="Header-purchaseDescription u-textTruncate">Buy Now</h2>
            <div class="Header-account" style="position: relative;"></div>
        </header>
    <ul>
    @foreach($products as $product)
            <li><label ><input type="radio" name="products" class="products" data-amount = "${{$product->initial_price }}"  value="{{$product->id}}">{{$product->title }} - ${{$product->initial_price }} </label></li>
    @endforeach
    </ul>
</div>
<form action="/payment" method="POST" id="form" style="display: none;">
    {{ csrf_field() }}
    <input type="hidden" name="id" id="product" value="">

    <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
            data-key="{{ $stripe['key'] }}"
            data-image="/images/Design-mini.png"
            data-name="The Mini-Mailer"
            data-amount="">
    </script>
</form>

<script>
    $(document).ready(function () {
        $("#buy-btm, #buy, #buy-l").on("click",function () {
            $("#order").fadeIn(200);
            $(".Overlay-Background").fadeIn(200);
            return false;

        })
        $(".Overlay-Background").on("click",function () {
            $("#order").fadeOut(200);
            $(".Overlay-Background").fadeOut(200);
            return false;

        })
        $(".products").on("change",function () {
           var id =  $(this).val();
           $("#product").val(id);
            $("#order").fadeOut();
            $(".Overlay-Background").fadeOut();
            $('.stripe-button-el').click();


           return false;

        })

    })

</script>