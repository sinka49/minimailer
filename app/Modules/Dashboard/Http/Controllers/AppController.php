<?php

namespace App\Modules\Dashboard\Http\Controllers;

use App\Models\Banner;
use App\Models\MailingHistory;
use App\Models\Product;
use App\Models\SmtpAccount;
use App\Models\TrainingPost;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Auth;
use App\Models\AffiliateUser;
use App\Models\Customer;
use App\Models\PageContent;
use App\Models\Payout;
use App\Jobs\QueueEmail;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use App\Models\Main;
use Log;

use PHPExcel_IOFactory;

use League\Flysystem\Exception;

use Cartalyst\Stripe\Stripe;

use Gate;

use App\Modules\Dashboard\Http\Controllers\Controller as BaseController;

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
        if (Gate::denies('signed', Auth::user())) {
            $signed = false;
            $stripe = Config::get('services.stripe');
            $products = Product::findAvailable()->orderBy('sort', 'asc')->get();
        }
        else $signed = true;
        $seo = $this->seo;
        return view( 'dashboard::app.home', compact('homePage','seo', 'signed', 'products', 'stripe') );
    }

    public function banners() {

        if (Gate::denies('signed', Auth::user()) && Auth::user()->role != "admin") {
            return view( 'dashboard::app.not-available', [
                'title' => 'The Mini-Mailer', 'seo'=>$this->seo

            ] );
        }

        $seo = $this->seo;
        $items = Banner::all();
        $user = AffiliateUser::where("user_id",Auth::user()->id)->first();
        return view( 'dashboard::app.banners', compact('items','seo', 'user') );
    }

    public function sender() {
        $smtpAccounts = SmtpAccount::findAllWithUser( Auth::user() );
        $subscription = Subscription::findActiveByUser(Auth::getUser());
        if (Gate::denies('signed', Auth::user()) && Auth::user()->role != "admin") {
            return view( 'dashboard::app.not-available', [
                'title' => 'The Mini-Mailer', 'seo'=>$this->seo

            ] );
        }
        $seo = $this->seo;
        if (Auth::user()->role == "admin"){
            return view( 'dashboard::app.sender', compact('smtpAccounts','seo') );
        }
        if( !$subscription ){
            $customer = Customer::where("user_id", Auth::user()->id)->first();
            if (count($customer)) {
                $stripeData = Config::get('services.stripe');
                $stripeE = new Stripe($stripeData["secret"], "2017-02-14");
                $params = ["limit"=>1];
                $subscriptions = $stripeE->subscriptions()->all($customer->customer_id,$params);

                 if (count($subscriptions)) {

                     return view( 'dashboard::app.sender', compact('smtpAccounts', 'seo') );
                 }

            }

        }
        else{
            return view( 'dashboard::app.sender', compact('smtpAccounts','seo') );
        }

    }

    public function training() {
        $seo = $this->seo;
        if (Gate::denies('signed', Auth::user()) && Auth::user()->role != "admin") {
            return view( 'dashboard::app.not-available', [
                'title' => 'The Mini-Mailer', 'seo'=>$seo,

            ] );
        }
        $posts = TrainingPost::findAllPublished();
        return view( 'dashboard::app.training', compact('posts', 'seo') );
    }

    /**
     * @param integer $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws Exception
     */
    public function showTraining($id) {
        if( !$id ){
            throw new Exception('There is no such post!');
        }
        $seo = $this->seo;

        $post = TrainingPost::where('id', $id)
            ->where('status', TrainingPost::STATUS_PUBLISHED)->first();
        if( !$post ){
            throw new Exception('There is no such post!');
        }
        return view( 'dashboard::app.training-show', compact('post', 'seo') );
    }

    public function subscription() {
        $seo = $this->seo;

        $products = Product::findAvailable()->orderBy('sort', 'asc')->get();
        $subscription = Subscription::findActiveByUser(Auth::getUser());
        $customer = Customer::where("user_id", Auth::user()->id)->first();
        if (count($customer)){
            $stripeData = Config::get('services.stripe');
            $stripeE = new Stripe($stripeData["secret"], "2017-02-14");
            if (count($subscription)){

                $subscriptionActDate = $stripeE->subscriptions()->find($customer->customer_id, $subscription->subscription_id);
                $subscription->start = date('m/d/Y' ,$subscriptionActDate["current_period_start"]);
                $subscription->end = date('m/d/Y' ,$subscriptionActDate["current_period_end"]);
                $diff = $subscriptionActDate["current_period_end"] - strtotime("now");
                $subscription->day_update = round($diff / (60*60*24));
                $subscription->status = "active";
            }
            else{
                $params = ["limit"=>1];
                $subscriptions = $stripeE->subscriptions()->all($customer->customer_id,$params);
                if (count($subscriptions)){
                    $subscription = new Subscription();
                    $subscription->start = date('m/d/Y' ,$subscriptions["data"][0]["current_period_start"]);
                    $subscription->end = date('m/d/Y' ,$subscriptions["data"][0]["current_period_end"]);
                    $diff = $subscriptions["data"][0]["current_period_end"] - strtotime("now");
                    $subscription->day_update = round($diff / (60*60*24));
                    $subscription->product_id = Product::where("renew_price", round($subscriptions["data"][0]["plan"]["amount"]/100))->first()->id;
                    if($subscriptions["data"][0]["cancel_at_period_end"]){
                        $subscription->status = "Available for the current period";
                    }
                }
            }
        }

        $stripe = Config::get('services.stripe');


        return view( 'dashboard::app.subscription', compact('stripe','products', 'subscription' , 'seo'));
    }



    public function sendEmails( Request $request ) {
        $emailAddresses = $request->get( 'email_addresses' );
        $smtpAccounts = $request->get( 'accounts' );
        $fromEmail = $request->get( 'from_email' );
        $fromName = $request->get( 'from_name' );
        $subject = $request->get( 'subject' );
        $body =  $request->get( 'body' );

        $response = Array( 'status' => false, 'message' => '' );

        if ( ! is_array( $emailAddresses ) ) {
            return response()->json( Array( 'status' => false, 'message' => 'Invalid or email not selected' ) );
        }

        $response = Array( 'status' => true );

        $mail = $this->addQueueEmail( $emailAddresses, $smtpAccounts, $fromEmail, $fromName, $subject, $body );
//        $mail = $this->sendBatchEmails( $request );
//        if ( $mail ) {
//            foreach ( $email_addresses as $k => $v ) {
//                $response[ $k ] = Array(
//                    'status'  => false,
//                    'email'   => $v,
//                    'message' => "Error: " . $mail->ErrorInfo
//                );
//            }
//        } else {
            foreach ( $emailAddresses as $k => $v ) {
                $response[ $k ] = Array(
                    'id'  => 1,
                    'status'  => true,
                    'email'   => $v,
                    'message' => "Sending..."
                );
            }
//        }

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


    }

    public function finance() {
        $seo = $this->seo;

        if (Gate::denies('signed', Auth::user()) && Auth::user()->role != "admin") {
            return view( 'dashboard::app.not-available', [
                'title' => 'The Mini-Mailer'

            ] );
        }
        $customer = Customer::where("user_id", Auth::user()->id)->first();
        $stripeData = Config::get('services.stripe');
        $stripeE = new Stripe($stripeData["secret"], "2017-02-14");
        $parameters = ["customer"=>$customer->customer_id];
        $transactions = $stripeE->charges()->all($parameters);

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
            $totalAm = $totalAm - $user->total_income;
        }
        $payout = Payout::where("user_id", Auth::user()->id)->orderBy("created_at", 'desc')->get();
        $access = true;
        if (count($payout)){
            foreach ($payout as $p){
                if ($p->status == 0){
                    $access = false;

                }
            }

        }




        return view( 'dashboard::app.finance', compact( 'transactions', 'totalAm', 'access', 'seo') );
    }

    public function mailingHistory() {
        $seo = $this->seo;

        if (Gate::denies('signed', Auth::user())&& Auth::user()->role != "admin") {
            return view( 'dashboard::app.not-available', [
                'title' => 'The Mini-Mailer', 'seo'=>$seo

            ] );
        }
        $mailingHistory = MailingHistory::findAllWithUser(Auth::getUser());
        return view( 'dashboard::app.mailing-history', compact( 'mailingHistory' , 'seo') );
    }

    /**
     * @param Request          $request
     *
     *
     */
    public function deletemailingHistory(Request $request){
        $id = $request->input("id");
        $res = MailingHistory::deleteById($id);
        return $res;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function smtpAccounts() {
        $seo = $this->seo;

        if (Gate::denies('signed', Auth::user())&& Auth::user()->role != "admin") {
            return view( 'dashboard::app.not-available', [
                'title' => 'The Mini-Mailer', 'seo'=>$seo

            ] );
        }
        $user = Auth::getUser();
        $smtpAccounts = SmtpAccount::findAllWithUser($user, true);
        return view( 'dashboard::app.smtp-accounts', compact( 'smtpAccounts','seo' ) );
    }

    /**
     * @param SmtpAccount|null $smtpAccount
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editSmtpAccount(SmtpAccount $smtpAccount = null) {
        $seo = $this->seo;

        return view( 'dashboard::app.edit-smtp-account', compact( 'smtpAccount' ,'seo' ) );
    }

    public function NewSmtpAccount(SmtpAccount $smtpAccount = null) {
        $seo = $this->seo;

        return view( 'dashboard::app.new-smtp-account', compact( 'smtpAccount', 'seo') );
    }
    /**
     * @param Request          $request
     * @param SmtpAccount|null $smtpAccount
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addSmtpAccount(Request $request) {
        $this->validate($request,[
            'email' => 'required|email|unique:smtp_accounts,email,'.Auth::user()->id,
            'password' => 'required',
            'host' =>  'required',
            'port' =>  'required|integer',
            'enabled'  =>  'required|boolean',
            'ssl_enabled' =>  'required',

        ]);
        $smtpAccount = new SmtpAccount();
        if( !$smtpAccount->id ){
            $smtpAccount->setUser( Auth::getUser() );
        }
        $smtpAccount->fill( $request->all() );
        $smtpAccount->password = $smtpAccount->password;
        $smtpAccount->save();
        return redirect("/dashboard/smtp-accounts");
    }

    /**
     * @param Request          $request
     * @param SmtpAccount|null $smtpAccount
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSmtpAccount(Request $request, SmtpAccount $smtpAccount = null) {
        $this->validate($request,[
            'email' => 'required|email|',
            'password' => 'required',
            'host' =>  'required',
            'port' =>  'required|integer',
            'enabled'  =>  'required|boolean',
            'ssl_enabled' =>  'required',

        ]);

        if( !$smtpAccount->id ){
            $smtpAccount->setUser( Auth::getUser() );
        }
        $smtpAccount->fill( $request->all() );
        $smtpAccount->save();
        return redirect("/dashboard/smtp-accounts");
    }

    /**
     * @param Request          $request
     *
     *
     */
    public function deleteSmtpAccount(Request $request){
        $id = $request->input("id");
        $res = SmtpAccount::deleteById($id);
        return $res;
    }

    public function users() {
        $seo = $this->seo;

        if ( ! Auth::isCurrentUserAdmin() ) {
            return redirect( URL::to( 'dashboard/home' ) );
        }
        $users = User::all();

        return view( 'dashboard::app.users', compact( 'users' ,'seo') );
    }

    public function acceptAffiliateProgram(Request $request) {
        $seo = $this->seo;

        $accept = $request->get('accept');

        if( $request->isMethod('post') ){
            if( $accept == 1 ){
                $user = AffiliateUser::where('user_id', Auth::user()->id)->first();
                if (count($user)){
                    $user->accepted_rules = 1;
                    $user->save();
                }
                else{
                    AffiliateUser::make( Auth::user(), null, null, $accept );
                }

                return redirect( URL::to('/dashboard/affiliate-program') );
            }
        }
        return view( 'dashboard::app.affiliate-program-accept', compact('accept','seo') );
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function affiliateProgram(Request $request) {
        $seo = $this->seo;

        if (Gate::denies('signed', Auth::user()) && Auth::user()->role != "admin") {
            return view( 'dashboard::app.not-available', [
                'title' => 'The Mini-Mailer', 'seo'=>$seo

            ] );
        }
        if( !AffiliateUser::isRulesAccepted() ){
            return redirect( URL::to('/dashboard/accept-affiliate-program') );
        }

        $au = AffiliateUser::findByCurrentUser();

        if( $request->isMethod('post') && $au ){
            $au->affiliate_name = $request->get('affiliate_name');
            $au->save();
        }

        $affiliateName = $au->getAffiliateName();
        $affiliateUrl = $au->getAffiliateUrl();
        $affiliateUsers = AffiliateUser::findByParentId( Auth::user()->id );


        $stripeData = Config::get('services.stripe');
        $stripeE = new Stripe($stripeData["secret"], "2017-02-14");
        $totalAm = 0;
        foreach ($affiliateUsers as $affiliateUser){
            $affiliateUser->name = User::find($affiliateUser->user_id)->name;
            $affiliateUser->email = User::find($affiliateUser->user_id)->email;
            $affiliateUser->total = AffiliateUser::where("parent_id", $affiliateUser->user_id)->count();
            if(count(Customer::where("user_id", $affiliateUser->user_id)->first())){
                $affiliateUser->status = "Upgraded";
                $parameters = ["customer" => Customer::where("user_id", $affiliateUser->user_id)->first()->customer_id];
                $charges = $stripeE->charges()->all($parameters);
                $totalAff = 0 ;
                $affiliateUser->totalAffWithUsers = 0;
                if (count($charges)){
                    foreach ($charges['data'] as $charge) {
                        $totalAff += $charge['amount']/ 100 / 100* Setting::find(1)->level1;
                    }
                    $affiliateUser->totalAff = $totalAff;
                    $affiliateUsersL2 = AffiliateUser::findByParentId( $affiliateUser->user_id );
                    foreach ($affiliateUsersL2 as $affiliateUserL2) {
                        if (count(Customer::where("user_id", $affiliateUserL2->user_id)->first())) {
                            $parameters = ["customer" => Customer::where("user_id", $affiliateUserL2->user_id)->first()->customer_id];
                            $charges = $stripeE->charges()->all($parameters);
                            $totalAff = 0 ;
                            if (count($charges)) {
                                foreach ($charges['data'] as $charge) {
                                    $totalAff += $charge['amount'] / 100 / 100 * Setting::find(1)->level2;
                                }
                                $affiliateUser->totalAffWithUsers += $totalAff;
                            }
                        }
                    }

                }
            }
            else{
                $affiliateUser->status = "Registered";
                $affiliateUser->totalAff = 0;
            }
        }
$level1 =  Setting::find(1)->level1; $level2 =  Setting::find(1)->level2;
        $smtpAccounts = SmtpAccount::findAllWithUser( Auth::user() );
        return view( 'dashboard::app.affiliate-program', compact('affiliateName', 'seo','smtpAccounts', 'affiliateUrl', 'affiliateUsers',  'totalAm', 'level1', 'level2') );
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

    /**
     * Adding a new piece of job of sending
     * @param User $user
     * @param      $options
     *
     * @return mixed
     */
    public function addSendEmailJob(User $user, $options)
    {
        Log::info("Request Cycle with Queues Begins");
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


        Log::info("Request Cycle with Queues Ends");
        return $options["job_id"];
    }

    public function loadXls(Request $r) {
        $file = $r->file('excel');

        $filePath = UploadedFile::createFromBase($file);

        $xlsRows = $this->getAllRowsFromXls($filePath);

        $allCounts = count($xlsRows);

        $checkedXlsRows = $this->checkXlsRows($xlsRows);
        $filledCheckedXlsRows = $this->fillSmtpDefaultData($checkedXlsRows);
        $savedAccounts = $this->saveSmtpData($filledCheckedXlsRows);

        if( $savedAccounts != $allCounts ){
            flash("$savedAccounts accounts was imported of $allCounts. Other had errors.")->warning();
        }else{
            flash("All $allCounts accounts was imported!")->success();
        }

        return back();
    }

    /**
     * @param $filledCheckedXlsRows
     *
     * @return int
     */
    private function saveSmtpData( $filledCheckedXlsRows ) {
        $userId = Auth::getUser()->id;

        $savedAccounts = 0;
        foreach($filledCheckedXlsRows as $row) {
            $check = SmtpAccount::where("email", $row["email"])->first();
            if (count($check)){
                continue;
            }
           else{
                $account = new SmtpAccount();
                $account->fill($row);
                $account->password = $account->password;
                $account->user_id = $userId;
                $account->enabled = 1;
                $account->save() && $savedAccounts++;
            }

        }

        return $savedAccounts;
    }

    /**
     * @param $checkedXlsRows
     *
     * @return array
     */
    private function fillSmtpDefaultData( $checkedXlsRows ) {
        $filled = [];

        foreach( $checkedXlsRows as $key => $row ){
            $filled[] = $this->fillRowSmtpDefaultData($row);
        }
        return $filled;
    }

    /**
     * @param $row
     *
     * @return array
     */
    private function fillRowSmtpDefaultData($row) {
            list(,$domain) = explode('@',$row[0]);
            $defaults = [
                'email'       => $row[0],
                'password'    => $row[1],
                'port'        => isset($row[2]) ? $row[2] : '587',
                'ssl_enabled' => isset($row[3]) ? $row[3] : 'tls',
                'host'        => isset($row[4]) ? $row[4] : "smtp.$domain",
                'proxy_id'    => isset($row[5]) ? $row[5] : 0
            ];
        return $defaults;
    }
    
    

    /**
     * Check if data is correct
     * @param $xlsRows
     *
     * @return mixed
     */
    private function checkXlsRows( $xlsRows ) {

        foreach( $xlsRows as $key => $row ){
            if( $this->checkRow($row) ){
                continue;
            }
            unset( $xlsRows[$key] );
        }
        return $xlsRows;
    }

    /**
     * Check a row if it's correct
     * @param $row
     *
     * @return bool
     */
    private function checkRow( $row ) {
        if( count( $row ) < 2 ){
            return false;
        }

        if( !filter_var($row[0], FILTER_VALIDATE_EMAIL) ){
            return false;
        }

        return true;
    }

    /**
     * @param string $filePath
     *
     * @return array
     */
    private function getAllRowsFromXls( $filePath ) {
        $rows = [];
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($filePath);
        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
        foreach ($objWorksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // This loops all cells,

            $row = [];
            foreach ($cellIterator as $cell) {
                $row[] = $cell->getValue();
            }
            $rows[] = $row;
        }
        return $rows;
    }
}