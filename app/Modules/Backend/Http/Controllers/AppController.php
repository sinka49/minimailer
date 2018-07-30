<?php

namespace App\Modules\Backend\Http\Controllers;

use App\Models\PageContent;
use App\Models\Payment_info;
use App\Models\Product;
use App\Models\SmtpAccount;
use App\Models\TrainingPost;
use App\Modules\Dashboard\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;
use Log;
use Cartalyst\Stripe\Stripe;
use App\Models\User;
use App\Models\Auth;
use App\Models\Customer;
use App\Models\Payout;
use App\Models\AffiliateUser;
use App\Models\Subscription;
use Travis\SMTP;
use App\Models\Main;
use App\Jobs\QueueEmail;
use App\Models\MailingHistory;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;
/**
 * Class AppController
 * @package App\Modules\Dashboard\Http\Controllers
 */
class AppController extends BaseController {
    protected $seo;
    public function __construct()
    {
        $s = Main::find(2);
        $this->seo = json_decode($s->data);
    }
    public function home() {
        $homePage = PageContent::getHomePage();
        $seo = $this->seo;
        return view( 'backend::app.home', compact('homePage', 'seo') );
    }

    public function updateHome(Request $request) {
        $this->validate($request,[
            'title' => 'string',
            'body' => 'required',
            'video' =>  'string',
        ]);


        PageContent::saveHomePage([
            'title' => $request->get('title'),
            'body' => $request->get('body'),
            'video' => $request->get('video'),
        ]);

        return redirect()->back();
    }

    public function sender() {
        $seo = $this->seo;
        return view( 'backend::app.sender', compact('seo') );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function training() {
        $posts = TrainingPost::all();
        $seo = $this->seo;
        return view( 'backend::app.training', compact('posts','seo') );
    }

    /**
     * @param integer/null $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editTraining($id = null) {

        $post = new TrainingPost();
        if( $id ){
            $post = TrainingPost::where('id', $id)->first();
        }
        $seo = $this->seo;
        return view( 'backend::app.training-edit', compact('post', 'seo') );
    }

    /**
     * @param Request $request
     * @param integer/null    $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateTraining(Request $request, $id = null) {

        $post = new TrainingPost();
        if( $id ){
            $post = TrainingPost::where('id', $id)->first();
        }
        $post->fill($request->all());

        if( $post->save() ){
            return redirect('opera/trainings');
        }

        return redirect('opera/trainings');
    }
    public function removeTraining($id = null) {
        if( $id ){
            $post = TrainingPost::where('id', $id)->first();
            $post->delete();
        }
        return back();
    }

    /**
     * @param integer/null $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editSubscription($id = null) {

        $post = new Product();
        if( $id ){
            $post = Product::where('id', $id)->first();
        }
        $seo = $this->seo;
        return view( 'backend::app.subscription-edit', compact('post' ,'seo') );
    }

    public function removeSubscription($id = null) {
        if( $id ){
            $post = Product::where('id', $id)->first();
            $post->delete();
        }
        return back();
    }

    /**
     * @param Request $request
     * @param integer/null    $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateSubscription(Request $request, $id = null) {

        $post = new Product();
        if( $id ){
            $post = Product::where('id', $id)->first();
        }
        $post->fill($request->all());

        if( $post->save() ){
            return redirect('opera/subscriptions');
        }

        return redirect('opera/subscriptions');
    }

    public function subscription() {
        $seo = $this->seo;
        $posts = Product::orderBy("sort",'asc')->get();
        return view( 'backend::app.subscription', compact('posts','seo') );
    }

    public function sendEmails( Request $request ) {
        $addresses = $request->get( 'email_addresses' );
        $email           = $request->get( 'email' );
        $subject          = $request->get( 'subject' );
        $body          = $request->get( 'body' );
        $response = Array( 'status' => false, 'message' => '' );
        $users = User::whereIn("id",$addresses)->get();
        $email_addresses = [];
        foreach ($users as $user) {
            $email_addresses[] = $user->email;
        }


        if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) === false ) {
            $response = Array( 'status' => false, 'message' => '' );
            $response['message'] = 'Invalid email';

            return response()->json( $response );
        }
        Log::alert("gg", [$email_addresses]);
        if ( ! is_array( $email_addresses ) ) {
            return response()->json( Array( 'status' => false, 'message' => 'Invalid or email not selected' ) );
        }
        $response = Array( 'status' => true );

        $smtp = SmtpAccount::where("user_id", Auth::user()->id)->get();
         $smtpAccounts = [];
        foreach ($smtp  as $s){
            $smtpAccounts[]= $s->id;
        }

        $mail = $this->addQueueEmail( $email_addresses, $smtpAccounts, $email, "Theminimailer", $subject, $body );

        if ( $mail ) {
            foreach ( $email_addresses as $k => $v ) {
                $response[ $k ] = Array(
                    'status'  => true,
                    'email'   => $v,
                    'message' => "message sent"

                );
            }
        } else {
            foreach ( $email_addresses as $k => $v ) {
                $response[ $k ] = Array(
                    'status'  => false,
                    'email'   => $v,
                    'message' => "Error"
                );
            }
        }

        return response()->json( $response );

    }

    private function addQueueEmail( &$emailAddresses, $smtpAccounts, $fromEmail, $fromName, $subject, $body ){
        $options = [
            'emailAddresses' => $emailAddresses,
            'accounts' => $smtpAccounts,
            'fromEmail' => $fromEmail,
            'fromName' => $fromName,
            'subject' => $subject,
            'body' =>  $body
        ];
        $user = Auth::user();

        $jobId = $this->addSendEmailJob($user, $options);
        return $jobId;

    }
    public function addSendEmailJob(User $user, $options)
    {

        $delay = 0;
        $pages = ceil(count($options["emailAddresses"]) / 50);
        $addreses = $options["emailAddresses"];

        $counter = 0;
        for ($i = 1; $i <= $pages;  $i++){
            $options["emailAddresses"] = [];
            if ($i == $pages){
                for ($j = 0; $counter < count($addreses); $j++){
                    $options["emailAddresses"][$j] = $addreses[$counter];
                    $counter++;
                }
            }
            else{
                $max = $counter + 50;
                for ($j = 0; $counter <= $max; $j++){
                    $options["emailAddresses"][$j] = $addreses[$counter];
                    $counter++;
                }
            }
            $options["job_id"] = uniqid(mt_rand(), true);

            MailingHistory::addList($options["emailAddresses"], $options["job_id"], $user->id);

            $job = (new QueueEmail($user, $options))->delay($delay);
            dispatch($job);


            $delay += 120;

        }



        return $options["job_id"];
    }



    public function finance() {
        $seo = $this->seo;
        $transactions = Payout::orderBy('created_at','asc')->paginate();
        foreach ($transactions as $t) {
            switch ($t->status){
                case 0: $t->stat = "Await";break;
                case 1: $t->stat = "Paid";break;
            }
            $t->email = User::find($t->user_id)->email;
            $t->paypal = Payment_info::where("user_id",$t->user_id)->first()->email_paypal;
        }

        return view( 'backend::app.finance', compact( 'transactions' , 'seo') );
    }
    public function financePaid($id = false) {
        if ($id){
            $item = Payout::find($id);
            $item->status = 1;
            $user = AffiliateUser::where("user_id",$item->user_id)->first();
            $user->sub = AffiliateUser::where("user_id",$item->user_id)->first();
            $res = $user->total_income*1 +  $item->amount;
            $user->total_income = $res;
            $user->save();
            $item->save();
        }

        return back();
    }
    public function financePaidRemove() {

        $items = Payout::where("status",1)->get();
        foreach ($items as $item) {
            $item->delete();
        }
        return back();
    }
    public function users() {
        if ( ! Auth::isCurrentUserAdmin() ) {
            return redirect( URL::to( 'backend/home' ) );
        }
        $seo = $this->seo;
        $users = User::paginate();
        foreach ($users as $user) {
            $user->sub = "No Subscription";
            $sub = Subscription::getSub($user->id);
            if (count($sub)){
                $user->sub = Product::find($sub->product_id)->title;
            }
        }

        return view( 'backend::app.users', compact( 'users', 'seo' ) );
    }

    public function usersAction(Request $request) {

        if ( ! Auth::isCurrentUserAdmin() ) {
            return redirect( URL::to( 'backend/home' ) );
        }
        $action = $request->input("action");
        switch ($action){
            case "remove" : self::removeUsers($request); break;
            case "block" : self::blockUsers($request); break;
            case "unblock" : self::unblockUsers($request); break;
            default: return redirect("/opera/users");

        }

        return redirect("/opera/users");
    }

    public function removeUsers(Request $request)
    {
        $array = $request->input("users");
        foreach ($array as $item) {
            User::destroy($item);
            $m = MailingHistory::where("user_id",$item)->get();

            if(count($m)){
                foreach ($m as $a){
                    $a->delete();
                }
            }
            $m = AffiliateUser::where("user_id",$item)->get();
            if(count($m)){
                foreach ($m as $a){
                    $a->delete();
                }
            }
            $m = Customer::where("user_id",$item)->get();
            if(count($m)){
                foreach ($m as $a){
                    $a->delete();
                }
            }
            $m = Payment_info::where("user_id",$item)->get();
            if(count($m)){
                foreach ($m as $a){
                    $a->delete();
                }
            }
            $m = Payout::where("user_id",$item)->get();
            if(count($m)){
                foreach ($m as $a){
                    $a->delete();
                }
            }
            $m = SmtpAccount::where("user_id",$item)->get();
            if(count($m)){
                foreach ($m as $a){
                    $a->delete();
                }
            }
            $m = Subscription::where("user_id",$item)->get();
            if(count($m)){
                foreach ($m as $a){
                    $a->delete();
                }
            }
        }

        return true;
    }
    public function blockUsers(Request $request)
    {
        $array = $request->input("users");
        foreach ($array as $item) {
           $user = User::find($item);
           $user->status = "block";
           $user->save();
        }

        return true;
    }
    public function unblockUsers(Request $request)
    {
        $array = $request->input("users");
        foreach ($array as $item) {
            $user = User::find($item);
            $user->status = "active";
            $user->save();
        }
        return true;
    }

    public function affiliateProgram() {
        if ( ! Auth::isCurrentUserAdmin() ) {
            return redirect( URL::to( 'backend/home' ) );
        }
        $users = AffiliateUser::paginate();

        foreach ($users as $user){
            $user->totalCount = AffiliateUser::where("parent_id", $user->user_id)->count();
            $user->totalActive = 0;
            $user->status = "Registered";
            $user->email = User::find($user->user_id)->email;
            $ac = User::find($user->user_id);
            $subscription = Subscription::findActiveByUser($ac);
            if (!count($subscription)){
                $customer = Customer::where("user_id",  $user->user_id)->first();
                if (count($customer)) {
                    $stripeData = Config::get('services.stripe');
                    $stripeE = new Stripe($stripeData["secret"], "2017-02-14");
                    $params = ["limit"=>1];
                    $subscriptions = $stripeE->subscriptions()->all($customer->customer_id,$params);

                    if (count($subscriptions)){
                        $subscription = new Subscription();
                        $subscription->product = Product::where("renew_price", round($subscriptions["data"][0]["plan"]["amount"]/100))->first()->title;
                        $subscription->start = date('m/d/Y' ,$subscriptions["data"][0]["created"]);
                        if($subscriptions["data"][0]["cancel_at_period_end"]){
                            $subscription->status = "Canceled";
                        }
                    }
                    else{
                        $subscription = null;
                    }
                }
            }
            else{
                $subscription->product = Product::where("id", $subscription->product_id)->first()->title;
                $subscription->status = "Active";
                $subscription->start = date('m/d/Y' , strtotime($subscription->created_at));
            }
            $user->subC = $subscription;
            $affiliateUsers = AffiliateUser::findByParentId($user->user_id);
            foreach ($affiliateUsers as $affiliateUser) {
               $sub = Subscription::getSub($affiliateUser->user_id);
                if (count($sub)){
                    $user->totalActive++;
                }
            }
            if (count(Customer::where("user_id", $user->user_id)->first())) {
                $user->status = "Upgraded";
            }
        }
        $seo = $this->seo;
        return view( 'backend::app.affiliate-program', compact( 'users' , 'seo') );

    }

    /**
     * AJAX
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadCsv( Request $request ) {
        $response_arr = Array( 'status' => false, 'message' => 'Invalid File Selected.' );
        $files        = $request->allFiles();

        if ( ! $files || ! isset( $files[0] ) ) {
            return response()->json( $response_arr );
        }

        $file = UploadedFile::createFromBase( $files[0] );

        $name    = $file->getClientOriginalName();
        $tmpName = $file->getRealPath();

        if ( empty( $name ) || empty( $tmpName ) || substr( $name, - 3, 3 ) != 'csv' ) {
            return response()->json( $response_arr );
        }

        $email_arr = $this->getEmailDataFromFile( $tmpName );

        return response()->json(
            Array(
                'status'  => true,
                'message' => 'Email addresses retrived successfully.',
                'data'    => $email_arr
            )
        );
    }

    /**
     * @param $file_name
     *
     * @return array|bool
     */
    private function getEmailDataFromFile( $file_name ) {
        if ( ( $handle = fopen( $file_name, "r" ) ) === false ) {
            return false;
        }

        $email_arr = Array();
        while ( ( $row = fgetcsv( $handle, 1000, "," ) ) !== false ) {
            if ( ! filter_var( $row[0], FILTER_VALIDATE_EMAIL ) !== false ) {
                continue;
            }
            $email_arr[] = $row[0];
        }
        fclose( $handle );

        return $email_arr;
    }
    public function settings(){

        $setting = Setting::find(1);
        return view("backend::app.settings", ["setting"=>$setting, 'seo' => $this->seo]);
    }
    public function setSettings(Request $request){
        $setting = Setting::find(1);
        $setting->fill( $request->all() );
        $setting->save();
        return back();
    }
}