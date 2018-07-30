<?php

namespace App\Models;

use Faker\Provider\cs_CZ\DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Class SmtpAccount
 * @package App\Models
 * @property integer id
 * @property integer user_id
 * @property integer proxy_id
 * @property string email
 * @property string password
 *
 * @property string host
 * @property string port
 * @property boolean enabled
 *
 * @property string ssl_enabled
 * @property integer period
 * @property DateTime last_use
 */

class SmtpAccount extends Model
{
    protected $fillable = ['proxy_id','email','password','host','port','enabled','ssl_enabled','period'];
    public $timestamps = false;
    /**
     * @param User $user
     * @param bool $all false - only active
     *
     * @return mixed
     */
    public static function findAllWithUser( User $user, $all = false ) {

        $cond = [
            'user_id' => $user->id
        ];
        if( $all === false ){
            $cond['enabled'] = 1;
        }

        return static::where($cond)
                   ->orderBy('id', 'desc')
                   ->get();
    }

    /**
     * @param      $id
     * @param User $user
     * @param bool $enabled
     *
     * @return mixed
     */
    public static function findOneWithUser( $id, User $user, $enabled = true ) {
        return static::where('user_id', $user->id)
                   ->where('enabled', $enabled)
                   ->where('id', $id)
                   ->first();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public static function findById( $id ) {
        return static::where('id', $id)
                   ->first();
    }

    /**
     * @param bool $enabled
     *
     * @return mixed
     */
    public static function findAll( $enabled = true ) {
        return static::where('enabled', $enabled)
                     ->get();
    }

    /**
     * @param User $user
     * @param      $accounts
     * @param bool $enabled
     *
     * @return mixed
     */
    public static function findOneWithUserFromRange( User $user, $accounts, $enabled = true ){

            $today = date("Y-m-d H:i:s");
            $item = SmtpAccount::where('user_id', $user->id)
                ->where('enabled', $enabled)
                ->whereIn('id', $accounts)
                ->inRandomOrder()
                ->first();
            if (count($item)){
            $item->last_use = $today;
            $item->save();
            }


        return $item;
    }

    /**
     * @param User $user
     */
    public function setUser( User $user ) {
        $this->user_id = $user->id;
    }

    /**
     *
     */
    public function setLastUseIsNow() {
        $this->last_use = date('Y-m-d H:i:s');
    }
    public static function deleteById($id) {
        return static::where("id", $id )->delete();
    }
}

