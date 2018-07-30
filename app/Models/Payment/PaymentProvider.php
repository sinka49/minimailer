<?php

namespace App\Models\Payment;

use Faker\Provider\cs_CZ\DateTime;
use Illuminate\Database\Eloquent\Model;
use App\Events\SavingPaymentEvent;


class PaymentProvider extends Model {
	/**
	 * @var array
	 */
	protected $error = null;
	/**
	 * @var Product
	 */
	protected $product = null;

	protected $charge = null;

	protected $events = [
		'saving' => SavingPaymentEvent::class,
	];

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
