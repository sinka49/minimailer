<?php

namespace App\Modules\Frontend\Http\Controllers;


use App\Models\Product;
use App\Models\GuestPayment;
use App\Models\Customer;


use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use League\Flysystem\Exception;

use Session;

use Illuminate\Support\Facades\Config;

use Illuminate\Support\Facades\Mail;

use Cartalyst\Stripe\Stripe;


class PaymentController extends BaseController
{

      public function stripe(Request $request)
    {

        $email = $request->input('stripeEmail');
        $stripeToken = $request->input('stripeToken');
        $id = $request->input('id');

        $product = Product::find($id);

        $desc = "$ $product->initial_price for a period of $product->renew_period  days";

        if (!$product || !$email || $stripeToken) {
            back();
        }

        $stripeData = Config::get('services.stripe');

        $stripe = new Stripe($stripeData["secret"], "2017-02-14");
        $customer = [];
        $issetCus = Customer::where("email", $email)->first();

        if (!count($issetCus)){
            $customer = $stripe->customers()->create([
                'email' => $email
            ]);
            $c = new Customer();
            $c->email = $email;
            $c->customer_id = $customer["id"];
            $c->save();
        }
        else {
            $customer["id"] = $issetCus["customer_id"];

        }




        $interval_count = round($product->renew_period / 30);

        $card = $stripe->cards()->create($customer['id'], $stripeToken);

        $price = round($product->initial_price, 2);
        $charge = $stripe->charges()->create([
            'customer' => $customer['id'],
            'currency' => 'USD',
            'amount'   => $price,
        ]);

        $price = round($product->renew_price, 2);
        $desc2 = "$ $product->renew_price for a period of $product->renew_period  days";

        $planId = md5(uniqid(""));
        $plan =  $stripe->plans()->create([
            'id'                    => $planId,
            'name'                  => $desc2,
            'amount'                => $price,
            'currency'              => 'USD',
            'interval'              => 'month',
            'interval_count'        => $interval_count,
        ]);


        $trial_end = time() + ($product->renew_period * 24 * 60 * 60);
        $subscription =  $stripe->subscriptions()->create($customer['id'], [
            'plan' => $planId,
            'trial_end' => $trial_end
        ]);





        Session::put('subscription', $subscription["id"]);
        Session::put('email', $email);

        $token = mb_strimwidth(base_convert(sha1(uniqid(mt_rand(), true)), 16, 19), 0, 19, "");
        Session::put("userPayment",  $token);

        $gPayment = new GuestPayment();
        $gPayment -> userPayment = $token;
        $gPayment -> email = $email;
        $gPayment -> item = $product->id;
        $gPayment -> save();

        $data = ['price'=> $product->initial_price,'period'=> $product->renew_period, 'token' => $token,'priceNew'=> $product->renew_price ];

        Mail::send('frontend::emails.reminder', $data, function ($m) use ($email) {
            $m->from(env('MAIL_USERNAME'), 'theminimailer');

            $m->to($email)->subject('Purchase on site theminimailer.com!');
        });

        return redirect('dashboard/register');
    }
}
