<?php

namespace App\Modules\Dashboard\Http\Controllers;



use App\Models\AffiliateUser;
use App\Models\Auth;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\Customer;
use App\Models\Payment_info;
use App\Models\Payout;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

use Session;

use Illuminate\Support\Facades\Config;

use Illuminate\Support\Facades\Mail;

use Cartalyst\Stripe\Stripe;
use App\Models\Setting;
use App\Models\Main;

class PaymentController extends BaseController
{
    protected $seo;
    public function __construct()
    {
        $s = Main::find(2);
        $this->seo = json_decode($s->data);
    }

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
            $c->user_id = Auth::user()->id;
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
        $count = strtotime("+".$product->renew_period." day");

        $exp_date = date('Y-m-d', $count);

        $sub = new Subscription();
        $sub -> user_id = Auth::user()->id;
        $sub -> exp_date = $exp_date;
        $sub -> product_id = $product->id;
        $sub -> subscription_id = $subscription["id"];
        $sub -> save();



        $data = ['price'=> $product->initial_price,'period'=> $product->renew_period, 'priceNew'=> $product->renew_price ];


        Mail::send('dashboard::emails.reminder', $data, function ($m) use ($email) {
            $m->from(env('MAIL_USERNAME'), 'theminimailer');

            $m->to($email)->subject('Purchase on site theminimailer.com!');
        });






        return redirect('/dashboard/subscription');
    }

    public function subscriptionCancel(Request $request) {


        $customer = Customer::where("user_id", Auth::user()->id)->first()->customer_id;
        $stripeData = Config::get('services.stripe');
        $stripeE = new Stripe($stripeData["secret"], "2017-02-14");
        $subscription_id = $request->input("subscription_id");
        $stripeE->subscriptions()->cancel($customer, $subscription_id, true);
        $oldSubscription = Subscription::where("subscription_id", $subscription_id)->first();
        $oldSubscription->delete();

        return redirect("/dashboard/subscription");
    }

    public function payout() {
        $payout = Payout::where("user_id", Auth::user()->id)->orderBy("created_at", 'desc')->get();
        if (count($payout)){
            foreach ($payout as $p){
                if ($p->status == 0){
                    Session::flash('message', "You have a pending payment");
                    return redirect("/dashboard/finance");
                }
            }

        }

        $pp = Payment_info::where("user_id",Auth::user()->id)->first();
        $stripeData = Config::get('services.stripe');
        $stripeE = new Stripe($stripeData["secret"], "2017-02-14");

        $affiliateUsers = AffiliateUser::findByParentId( Auth::user()->id );
        $totalAm = 0;
        foreach ($affiliateUsers as $affiliateUser){
           if(count(Customer::where("user_id", $affiliateUser->user_id)->first())){
               $parameters = ["customer" => Customer::where("user_id", $affiliateUser->user_id)->first()->customer_id];
               $charges = $stripeE->charges()->all($parameters);
               $totalAff = 0 ;
               if (count($charges)){
                   foreach ($charges['data'] as $charge) {
                       $totalAff += $charge['amount']/ 100 / 100 * Setting::find(1)->level1;
                   }
                   $affiliateUsersL2 = AffiliateUser::findByParentId( $affiliateUser->user_id );
                   foreach ($affiliateUsersL2 as $affiliateUserL2) {
                       if (count(Customer::where("user_id", $affiliateUserL2->user_id)->first())) {
                           $parameters = ["customer" => Customer::where("user_id", $affiliateUserL2->user_id)->first()->customer_id];
                           $charges = $stripeE->charges()->all($parameters);
                           if (count($charges)) {
                               foreach ($charges['data'] as $charge) {
                                   $totalAff += $charge['amount'] / 100 / 100 * Setting::find(1)->level2;
                               }
                               $affiliateUser->totalAffWithUsers += $totalAff;
                           }
                       }
                   }
               }
               $totalAm += $totalAff;
           }

        }
        if ($totalAm>0){
           $user = AffiliateUser::where("user_id",Auth::user()->id)->first();
           $totalAmNow = $totalAm - $user->total_income;
        }

        if ($totalAmNow<60){
            Session::flash('message', "Your available balance is less than $ 60");
            return redirect("/dashboard/finance");
        }
        if (!count($pp)){

            return redirect("/dashboard/profile");
        }




        return view( 'dashboard::app.payout', [ "totalAm"=>$totalAm, "totalAmNow"=>$totalAmNow,
            "income"=>$user->total_income, "pp"=>$pp, 'seo'=>$this->seo] );

    }


    public function sendPayOut(Request $request) {
        $paypal = $request->input("paypal");
        $pp = Payment_info::where("user_id",Auth::user()->id)->first();
        if (count($pp)) {
            if (!empty($pp->email_paypal)) {
                $paypal = $pp->email_paypal;
            }
            else{
                $paypal = "";
            }
        }


        $amount = $request->input("amount");



        $payout = new Payout();
        $payout->user_id = Auth::user()->id;
        $payout->amount = $amount;
        $payout->status = 0;
        $payout->save();

        $data = ["email"=> Auth::user()->email, "amount"=>$amount, "paypal"=>$paypal ];

        Mail::send('dashboard::emails.payout', $data, function ($m)  {
            $m->from(env('MAIL_USERNAME'), 'theminimailer');

            $m->to(env('MAIL_SUPPORT'))->subject("Theminimailer Payout");
        });

        Session::flash('message', "Thank you, your request will be processed within 24 hours");

        return redirect("/dashboard/finance");
    }


}
