<?php

namespace App\Modules\Dashboard\Http\Controllers;





use App\Modules\Dashboard\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Main;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Session;



class SupportController extends BaseController
{
    protected $seo;
    public function __construct()
    {
        $s = Main::find(2);
        $this->seo = json_decode($s->data);
    }

    public function index()
    {

        return view( 'dashboard::app.support',['seo'=>$this->seo]);

    }

    public function getMessage(Request $request)
    {
        $subject = $request->input("subject");
        $text = $request->input("body");
        $from = $request->input("from_name");
        $name = $request->input("from_email");

        $data = ["subject"=> $subject, "text"=>$text, "from"=>$from, "name" => $name ];

        Mail::send('dashboard::emails.support', $data, function ($m) use ($subject) {
            $m->from(env('MAIL_USERNAME'), 'theminimailer');

            $m->to(env('MAIL_SUPPORT'))->subject($subject);
        });

        Session::flash('message', "Thank you for contacting us. The administrator will contact you");


        return redirect("/dashboard/support");
    }

}
