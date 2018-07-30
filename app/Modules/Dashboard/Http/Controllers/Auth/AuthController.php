<?php

namespace App\Modules\Dashboard\Http\Controllers\Auth;

use App\Models\AffiliateUser;
use App\Models\Customer;
use App\Models\Payment\PaymentProvider;
use App\Models\Product;
use Illuminate\Support\Facades\Session;
use Validator;
use App\Models\User;
use App\Models\Auth;
use App\Modules\Dashboard\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use App\Models\Main;
use App\Models\GuestPayment;
use App\Models\Subscription;
class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers,
        ThrottlesLogins;


    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = 'dashboard/home';

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function showLoginForm(){
        if( !Auth::guard($this->getGuard())->guest() ){
            return redirect($this->redirectTo);
        }
        $seo = Main::find(2);
        return view('dashboard::auth.login', compact('seo'));
    }

    public function showRegistrationForm(Request $request)
    {
        if( !Auth::guard($this->getGuard())->guest() ){
            return redirect('dashboard/home');
        }
        $seo = Main::find(2);
        return view('dashboard::auth.register', compact('seo'));
    }


    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        Mail::send('dashboard::emails.hello',[], function ($m) use ($request) {
            $m->from(env('MAIL_USERNAME'), 'Theminimailer');
            $m->to($request->input("email"))->subject("Signing up with The Mini-Mailer");
        });

        Auth::guard($this->getGuard())->login($this->create($request->all()));


        $this->checkCreateAffiliatedUser($request);
        $this->checkCreatePayment($request);




        return redirect($this->redirectTo);
    }


    private function checkCreateAffiliatedUser( Request $request ) {
        if( !$request->session()->has('affiliateId') ){
            return false;
        }
        $affiliateId = $request->session()->get('affiliateId');
        $parentId = AffiliateUser::where('affiliate_name', $affiliateId)->first()->user_id;
        $parent = User::find($parentId);
        if( !$parent || !AffiliateUser::make( Auth::user(), $parent ) ){
            return false;
        }
        $request->session()->forget('affiliateId');



        return true;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    private function checkCreatePayment( Request $request ) {
        if( ! Session::has('userPayment') ){
            return false;
        }

        $paymentId = Session::get('userPayment');
        $payment = GuestPayment::getPayment($paymentId);
        if( !count($payment) ) {
            return false;
        }
        $product = Product::where("id",$payment->item)->first();
        $count = strtotime("+".$product->renew_period." day");

        $exp_date = date('Y-m-d', $count);

        $subscription = new Subscription();
        $subscription->user_id = Auth::user()->id;
        $subscription->product_id = $product->id;
        $subscription->subscription_id = Session::get('subscription');
        $subscription->exp_date = $exp_date;
        $subscription->save();

        $customer = Customer::where("email", Session::get('email'))->first();
        $customer->user_id = Auth::user()->id;
        $customer->save();

        $payment->delete();

        $request->session()->forget('userPayment');
        $request->session()->forget('subscription');
        $request->session()->forget('email');

        return true;
    }


}
