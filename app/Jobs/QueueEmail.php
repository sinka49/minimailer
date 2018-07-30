<?php

namespace App\Jobs;

use App\Jobs\Job;

use App\Models\MailingHistory;
use App\Models\SmtpAccount;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use PHPMailer;
use Log;
use App\Models\User;
use App\Models\Auth;
use Illuminate\Support\Facades\DB;
use phpmailerException;
use Travis\SMTP;
use Illuminate\Support\Facades\Config;


class QueueEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var User
     */
    private $user = null;
    /**
     * @var array
     */
    private $options = [];
    /**
     * @var boolean
     */
    private $sendResult = false;
    /**
     * @var Exception
     */
    private $sendError = null;


    public function __construct(User $user, $options = [])
    {
        $this->user = $user;
        $this->options = $options;

        Log::info("Added email", ['user' => $this->user, 'options' => $this->options]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SMTP $mailer)
    {


        $smtpAccount = SmtpAccount::findOneWithUserFromRange( $this->user, $this->options['accounts'] );


        if( !$smtpAccount ){
            if (Auth::user()->role == "admin"){
                $smtpAccount = new SmtpAccount();
                $smtpAccount->host        =  env("MAIL_SUPPORT_HOST");
                $smtpAccount->port        =  env("MAIL_SUPPORT_PORT");
                $smtpAccount->ssl_enabled =  env("MAIL_SUPPORT_ENCRYPTION");
                $smtpAccount->email       =  env("MAIL_SUPPORT_USERNAME");
                $smtpAccount->password    =  env("MAIL_SUPPORT_PASSWORD");
            }
            else{
                return false;
            }

        }


        $res = $this->sendEmail($mailer, $smtpAccount);

        $status = !$res ? MailingHistory::STATUS_FAIL : MailingHistory::STATUS_SUCCESS;


        MailingHistory::updateSmtpStatus($smtpAccount->email, $this->options["job_id"], $status);

        $smtpAccount->setLastUseIsNow();
        $smtpAccount->save();



        if( !$res ){

            if( $this->sendError ){

            }

            return false;
        }

        return true;
    }

    /**
     * @param PHPMailer $mailer
     * @param           $smtpAccount
     *
     * @return bool
     */
    private function sendEmail( SMTP $mailer, $smtpAccount ) {

        $config = [
            'debug_mode' => true,
            'default' => 'primary',
            'connections' => [
                'primary' => [
                    'host' => $smtpAccount->host,
                    'port' => !$smtpAccount->port ? 25 : $smtpAccount->port,
                    'secure' => null, // null, 'ssl', or 'tls'
                    'auth' => true, // true if authorization required
                    'user' => $smtpAccount->email,
                    'pass' => $smtpAccount->password,

                ],
            ],

            'localhost' => 'localhost', // rename to the URL you want as origin of email
        ];

        if (in_array($smtpAccount->ssl_enabled, ['ssl','tls'])) {
            $config['connections']['primary']['secure'] = $smtpAccount->ssl_enabled;
        }

        $mailer = new SMTP($config);

        $mailer->from($this->options['fromEmail'], $this->options['fromName']); // email is required, name is optional
        $mailer->subject( $this->options['subject']);
        $mailer->body($this->options['body']);
        //$mailer->to(array_shift($this->options['emailAddresses']));
        $mailer->reply($this->options['fromEmail'], $this->options['fromName']);

        if ($smtpAccount->host != "smtp.gmail.com"){
            $mailer->from($smtpAccount->email, $this->options['fromName']); // email is required, name is optional
            $mailer->reply($smtpAccount->email, $this->options['fromName']);
        }

        if(count($this->options['emailAddresses'])){
            foreach ( $this->options['emailAddresses'] as $k => $v ) {
                $mailer->bcc( $v );
            }
        }
        $this->sendResult = $mailer->send();


        return $this->sendResult;
    }

    /**
     * The job failed to process.
     *

     * @return void
     */
    public function failed()
    {
        Log::alert("Email sending failed", ['exception' => $this->sendError]);
    }
}