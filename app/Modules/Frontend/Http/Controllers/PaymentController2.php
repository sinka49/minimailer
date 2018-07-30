<?php

namespace App\Modules\Frontend\Http\Controllers;


use App\Models\Payment\Payment;
use App\Models\Product;
use App\Models\Payment\StripePaymentProvider;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use App\Models\Transaction;
use Illuminate\Support\Facades\Mail;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use League\Flysystem\Exception;
use App\Models\GuestPayment;
use Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;

use Illuminate\Support\Facades\Validator;

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
//use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
//use PayPal\Api\Transaction as Transaction;
use PayPal\Api\FundingInstrument;
use PayPal\Api\PaymentCard;
use PayPal\Exception\PayPalConnectionException;

use PayPal\Api\ChargeModel;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;
use PayPal\Api\Agreement;
use PayPal\Api\PayerInfo;
use PayPal\Api\CreditCard;
use DateTime;
use  DateTimeZone;
use PayPal\Api\Patch;
use PayPal\Common\PayPalModel;
use PayPal\Api\PatchRequest;


use  PayPal\Api\AgreementStateDescriptor;
use PayPal\Api\ShippingAddress;
class PaymentController extends BaseController
{

    private $_api_context;


    public function __construct()
    {

        // setup PayPal api context
        //$paypal_conf = Config::get('paypal');
        //$this->_api_context = new ApiContext(new OAuthTokenCredential($paypal_conf['client_id'], $paypal_conf['secret']));
        //$this->_api_context->setConfig($paypal_conf['settings']);
    }


    public function stripe(Request $request)
    {

        $email = $request->input('stripeEmail');
        $stripeToken = $request->input('stripeToken');
        $id = $request->input('id');

        $product = Product::find($id);

        if (!$product || !$email || $stripeToken) {
            back();
        }

        $paymentProvider = new StripePaymentProvider();
        $charge = $paymentProvider->makeFromProductInitialPrice($product, $stripeToken);

        if( !$charge ){
            $error = $paymentProvider->getError();
            var_dump($error->getMessage());
            throw new Exception('Error during payment. Please, appeal to our support team');
            //TODO: make flash
        }

        $payment = new Transaction();
        $payment->storeDebitNullUser($product->initialPrice, 'Stripe payment');
        $payment->setServiceResponse( $charge->getLastResponse() );

        if( !$paymentProvider->isChangeSuccess() ){
            throw new Exception('Error during payment. Please, appeal to our support team');
        }

        $request->session()->set('paymentId', $payment->id);
        $request->session()->set('productId', $product->id);

        //        $this->sendRegistrationEmail($request);

        return redirect('dashboard/register');
    }
    public function postPayment(Request $request)
    {
        $product_id = $request->input("product");
        $card_number = $request->input("card_number");
        $csv_number = $request->input("cvv");
        $expiry_date_m = $request->input("expiry_date_m");
        $expiry_date_y = 2000 + $request->input("expiry_date_y");

        $type_card = $request->input("type_card");
        $email = $request->input("email");


        $product = Product::where("id", $product_id)->first();
        $desc = "A subscription for $ $product->initial_price for a period of $product->renew_period  days";
        /*if(count($fio)<2){
            return redirect("/")->with('error', 'name on card failed');
        }*/

        $validator = Validator::make(
            array(
                'csv' => $csv_number,
                'email' => $email
            ),
            array(
                'csv' => 'required|min:3',
                'email' => 'required|email'
            )
        );
        if ($validator->fails())
        {
            return redirect("/")->with('error', 'Data failed');
        }

        /*$cycles = round($product->renew_period / 30);

        $data = [
            "card_number" => $card_number,
            "csv_number" => $csv_number,
            "expiry_date_m" => $expiry_date_m,
            "expiry_date_y" => $expiry_date_y,
            "type_card" => $type_card,
            "email" => $email
            ];

        $plan = self::createPlan($cycles, $product, $desc);

        $plan = self::patchPlan($plan);

        $agreement = self::createAgreement($plan, $data, $desc);


        echo "Created Plan";*/



        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(URL::route('payment.status'))
            ->setCancelUrl(URL::route('payment.status'));

        $card = new PaymentCard();
        $card->setType($type_card)
            ->setNumber($card_number)
            ->setExpireMonth($expiry_date_m)
            ->setExpireYear("".$expiry_date_y)
            ->setCvv2($csv_number)
            ->setFirstName($fio[0])
            ->setBillingCountry("US")
            ->setLastName($fio[1]);


        $fi = new FundingInstrument();
        $fi->setPaymentCard($card);

        $payer = new Payer();
        $payer->setPaymentMethod("credit_card")
            ->setFundingInstruments(array($fi));

        $item1 = new Item();
        $item1->setName($product->title)
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice($product->initial_price);


        $itemList = new ItemList();
        $itemList->setItems(array($item1));


        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal($product->initial_price);



        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList);
           // ->setInvoiceNumber(uniqid());


        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setTransactions(array($transaction));

        try {
            $payment->create($this->_api_context);
        } catch (PayPalConnectionException $ex) {
            echo $ex->getCode(); // Prints the Error Code
            exit(1);
        } catch (Exception $ex) {
            exit(1);
        }

        //add payment ID to session
        Session::put('paypal_payment_id', $payment->getId());
        Session::put('email', $email);
        return redirect("/payment/status");

    }
    public function createPlan($cycles, $product, $desc)
    {
        $plan = new Plan();
        $plan->setName('Minimailer subscription')
            ->setDescription($desc)
            ->setType('fixed');

        $paymentDefinition = new PaymentDefinition();
        $paymentDefinition->setName('Regular Payments')
            ->setType('REGULAR')
            ->setFrequency('DAY')
            ->setFrequencyInterval("1")
            ->setCycles($cycles)
            ->setAmount(new Currency(array('value' => $product->initial_price, 'currency' => 'USD')));



        $merchantPreferences = new MerchantPreferences();
        $baseUrl = url('/');

        $merchantPreferences->setReturnUrl("$baseUrl/payment/status?success=true")
            ->setCancelUrl("$baseUrl/payment/status?success=false")
            ->setAutoBillAmount("yes")
            ->setInitialFailAmountAction("CONTINUE")
            ->setMaxFailAttempts("3")
            ->setSetupFee(new Currency(array('value' =>  $product->initial_price, 'currency' => 'USD')));


        $plan->setPaymentDefinitions(array($paymentDefinition));
        $plan->setMerchantPreferences($merchantPreferences);

        try {
            $plan = $plan->create($this->_api_context);
        } catch (PayPalConnectionException $ex) {
            echo $ex->getCode(); // Prints the Error Code
            exit(1);
        } catch (Exception $ex) {
            exit(1);
        }
        echo $plan->getId();
        echo "<br>";
        echo "Plan created";
        return $plan;
    }

    public function patchPlan($createdPlan)
    {
        try {
            $patch = new Patch();

            $value = new PayPalModel('{
	       "state":"ACTIVE"
	     }');

            $patch->setOp('replace')
                ->setPath('/')
                ->setValue($value);
            $patchRequest = new PatchRequest();
            $patchRequest->addPatch($patch);

            $createdPlan->update($patchRequest, $this->_api_context);

            $plan = Plan::get($createdPlan->getId(), $this->_api_context);
        } catch (PayPalConnectionException $ex) {
            echo $ex->getCode(); // Prints the Error Code
            exit(1);
        } catch (Exception $ex) {
            exit(1);
        }
        echo "<br>";
        echo "Updated the Plan to Active State";
        echo "<br>";
        echo $plan->getId();
        dump($patchRequest);
        dump($plan);
        return $plan;

    }

    public function createAgreement($createdPlan, $data, $desc)
    {
        date_default_timezone_set('Europe/Moscow');
        $agreement = new Agreement();
        $startDate = date('Y-d-m\Th:i:s\Z');
        echo $startDate;
        $agreement->setName('Minimailer')
            ->setDescription($desc)
            ->setStartDate("2018-03-07T17:45:57Z");



        $plan = new Plan();
        $plan->setId($createdPlan->getId());
        $agreement->setPlan($plan);

        $payer = new Payer();
        $payer->setPaymentMethod('credit_card')
            ->setPayerInfo(new PayerInfo(array('email' => $data['email'])));

        $card = new CreditCard();
        $card->setType($data['type_card'])
            ->setNumber($data['card_number'])
            ->setExpireMonth($data['expiry_date_m'])
            ->setExpireYear("".$data['expiry_date_y'])
            ->setCvv2($data['csv_number']);

        $fundingInstrument = new FundingInstrument();
        $fundingInstrument->setCreditCard($card);

        $payer->setFundingInstruments(array($fundingInstrument));
        $agreement->setPayer($payer);

        dump($agreement);
        try {

            $agreement = $agreement->create($this->_api_context);
        } catch (PayPalConnectionException $ex) {
            echo $ex->getCode();
            echo $ex->getData();
            die($ex);
        } catch (Exception $ex) {
            die($ex);
        }
    }

    public function getPaymentStatus()
    {
        $payment_id = Session::get('paypal_payment_id');
        $email = Session::get('email');
        // clear the session payment ID

        Session::remove('paypal_payment_id');
        Session::remove('email');
        try {

        $payment = Payment::get($payment_id, $this->_api_context);

        } catch (PayPalConnectionException $ex) {

            echo $ex->getCode(); // Prints the Error Code
            echo $ex->getData(); // Prints the detailed error message
            die();

        } catch (Exception $ex) {

            die();

        }



        if ($payment->getState() != 'approved'){
            return redirect("/")->with('error', 'Payment failed');
        }

        $trans =  $payment->getTransactions();

        $items = $trans[0]->item_list->items;
        $price = round($items[0]->price);

        $product = Product::where("initial_price" , $price )->first();

        if (count($product)==0){
            $product = Product::find(1);
        }

        $token = mb_strimwidth(base_convert(sha1(uniqid(mt_rand(), true)), 16, 19), 0, 19, "");
        Session::put("userPayment",  $token);

        $gPayment = new GuestPayment();
        $gPayment -> userPayment = $token;
        $gPayment -> email = $email;
        $gPayment -> item = $product->id;
        $gPayment->save();

        $data = ['price'=> $product->initial_price,'period'=> $product->renew_period, 'token' => $token];

        Mail::send('frontend::emails.reminder', $data, function ($m) use ($email) {
            $m->from(env('MAIL_USERNAME'), 'theminimailer');

            $m->to($email)->subject('Purchase on site theminimailer.com!');
        });


       /* return redirect("/")
            ->with('success', 'Payment approved');*/
    }
}
