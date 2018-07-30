<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Log;
/**
 * Class MailingHistory
 * @package App\Models
 * @property integer  id
 * @property integer  user_id
 * @property integer  smtp_account_email
 * @property string   recipient_email
 * @property integer  job_id
 * @property string   status
 * @property DateTime date
 */
class MailingHistory extends Model {

    public $timestamps = false;
    public $table = 'mailing_history';

    const STATUS_IN_QUEUE = 10;
    const STATUS_SENDING = 40;
    const STATUS_SUCCESS = 60;
    const STATUS_FAIL = 80;

    /**
     * @param User    $user
     * @param integer $jobId
     *
     * @return mixed
     */
    public static function findAllWithUser( User $user, $jobId = null, $limit = 20, $start = 0 ) {

        $cond = [
            'user_id' => $user->id,
        ];

        if ( $jobId ) {
            $cond['job_id'] = $jobId;
        }

        return static::where( $cond )->orderBy('date',"desc")
                     ->paginate($limit);
    }

    /**
     * @param string $recipientEmails
     * @param int    $jobId
     * @param int    $status
     */
    public static function addList( &$recipientEmails, $jobId, $user_id, $status = self::STATUS_IN_QUEUE ) {
        foreach ( $recipientEmails as &$r ) {
            self::add( $r, $jobId, $user_id, $status );
        }
    }

    /**
     * @param string $recipientEmail
     * @param int    $jobId
     * @param int    $status
     * @param string $smtpAccountEmail
     */
    public static function add( $recipientEmail, $jobId, $user_id, $status = self::STATUS_IN_QUEUE, $smtpAccountEmail = '' ) {
        $mh = new self();

        $mh->recipient_email    = $recipientEmail;
        $mh->smtp_account_email = $smtpAccountEmail;

        $mh->status = $status;
        $mh->user_id = $user_id;
        $mh->job_id = $jobId;
        $mh->date   = date( 'Y-m-d H:i:s' );

        $mh->save();
    }

    /**
     * @param $smtpAccountEmail
     * @param $status
     * @param $jobId
     */
    public static function updateSmtpStatus( $smtpAccountEmail, $jobId, $status = self::STATUS_FAIL ) {
        $items = MailingHistory::where('job_id', $jobId)->get();

        foreach ($items as $item){
            $item->smtp_account_email = $smtpAccountEmail;
            $item->status = $status;
            $item->save();
        }
        return true;
    }

    public static function deleteById($id) {
        return static::where("user_id", $id )->delete();
    }
}

