<?php

namespace App\Modules\Dashboard\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use App\Models\Subscription;
use App\Models\Customer;
use Cartalyst\Stripe\Stripe;
use Illuminate\Support\Facades\Config;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);


        Gate::define('payment_done', function ($user) {
            $token = Session::get('userPayment');

            return isset($token);
        });

        Gate::define('signed', function ($user) {
            $subscription = Subscription::findActiveByUser($user);
            if( !$subscription ){
                $customer = Customer::where("user_id",$user->id)->first();
                if (count($customer)) {
                    $stripeData = Config::get('services.stripe');
                    $stripeE = new Stripe($stripeData["secret"], "2017-02-14");
                    $params = ["limit"=>1];
                    $subscriptions = $stripeE->subscriptions()->all($customer->customer_id,$params);

                    if (!count($subscriptions)) {

                        return false;
                    }
                    else return true;

                }
                return false;

            }
            else return true;


        });
    }


}
