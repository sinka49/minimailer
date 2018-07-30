<?php

namespace App\Modules\Dashboard\Http\Controllers;



use App\Models\AffiliateUser;
use App\Models\Auth;
use App\Models\Payment_info;

use App\Models\Main;
use App\Modules\Dashboard\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;


use App\Models\User;



class ProfileInfoController extends BaseController
{
    protected $seo;
    public function __construct()
    {
        $s = Main::find(2);
        $this->seo = json_decode($s->data);
    }
    public function index()
    {
        $pp = Payment_info::where("user_id",Auth::user()->id)->first();
        $ref = AffiliateUser::getAffiliateUrl();



        return view( 'dashboard::app.profile', [ "pp"=>$pp, "ref"=>$ref ,'seo'=>$this->seo] );

    }

    public function setPayPal(Request $request)
    {
        $this->validate($request,[
        'email_paypal' => 'required|email|max:255'
        ]);
        $email  = $request->input("email_paypal");
        $res = Payment_info::setPPemail(Auth::user()->id, $email);



        return redirect("/dashboard/profile");
    }

    public function setCardNumber(Request $request) {

        $card  = $request->input("card_number");
        $res = Payment_info::setCard(Auth::user()->id, $card);
        return redirect("/dashboard/profile");

    }

    public function setName(Request $request) {


        $name  = $request->input("name");
        $res = User::setName(Auth::user()->id, $name);
        return redirect("/dashboard/profile");

    }

    public function setPassword(Request $request) {
        $this->validate($request,[
            'password' => 'required|min:6',
        ]);

        $pass  = $request->input("password");
        $res = User::setPassword(Auth::user()->id, $pass);
        return redirect("/dashboard/profile");

    }

}
