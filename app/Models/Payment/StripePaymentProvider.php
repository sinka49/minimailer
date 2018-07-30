<?php

namespace App\Models\Payment;

use App\Models\Product;
use Stripe\Charge;
use Stripe\Stripe;
use Illuminate\Support\Facades\Config;
use Stripe\Error\Card;
use Stripe\Error\InvalidRequest;
use Stripe\Error\Authentication;
use Stripe\Error\ApiConnection;
use Error;

class StripePaymentProvider extends PaymentProvider
{
	/**
	 * @var Charge
	 */
	public $charge;

	/**
	 * @param Product $product
	 * @param         $stripeToken
	 *
	 * @return Charge|null
	 */
    public function makeFromProductInitialPrice(Product $product, $stripeToken)
    {
        return $this->make($product->initialPrice, $product->currency, $product->description, $stripeToken);
    }

	/**
	 * @param $amount
	 * @param $currency
	 * @param $description
	 * @param $stripeToken
	 *
	 * @return Stripe_Charge|null
	 */
    public function make($amount, $currency, $description, $stripeToken)
    {

	    Stripe::setApiKey(Config::get('services.stripe')['secret']);
	    Stripe::$apiBase = "https://api-tls12.stripe.com";

        try {
            $charge = Charge::create(array(
                "amount"      => $amount,
                "currency"    => $currency,
                "card"        => $stripeToken,
                "description" => $description,
                "source" => $stripeToken
            ));
        } catch (Card $e) {
            $this->setError($e);
            return null;
        } //catch the errors in any way you like
        catch (InvalidRequest $e) {
            // Invalid parameters were supplied to Stripe's API
            $this->setError($e);
            return null;
        } catch (Authentication $e) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $this->setError($e);
            return null;
        } catch (ApiConnection $e) {
            // Network communication with Stripe failed
            $this->setError($e);
            return null;
        } catch (Error $e) {
            // Display a very generic error to the user, and maybe send
            // yourself an email
            $this->setError($e);
            return null;
        } catch (Exception $e) {
            // Something else happened, completely unrelated to Stripe
            $this->setError($e);
            return null;
        }

        $this->setCharge($charge);

        return $this->getCharge();
    }

    /**
     * @param Stripe_Charge $charge
     */
    public function setCharge($charge)
    {
        $this->charge = $charge;
    }

    /**
     * @return Stripe_Charge
     */
    public function getCharge()
    {
        return $this->charge;
    }

    /**
     * @return bool
     */
    public function isChangeSuccess() {
        if( !$this->charge ){
            return false;
        }

        $resp = $this->charge->getLastResponse();

        return $resp->paid === true;
    }

    /**
     * @return null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param $err
     */
    public function setError($err)
    {
        $this->error = $err;
    }

    /**
     * @return null
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param null $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }
}
