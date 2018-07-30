<form action="/dashboard/payment" method="POST" id="form" style="display: none;">
    {{ csrf_field() }}
    <input type="hidden" name="id" id="product" value="">

    <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
            data-key="{{ $stripe['key'] }}"
            data-image="/images/Design-mini.png"
            data-name="The Mini-Mailer"
            data-amount=""
            data-email="{{Auth::user()->email}}"
    >
    </script>
</form>

