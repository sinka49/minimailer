<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTime;
use App\Models\User;

/**
 * Class Product
 * @package App\Models
 * @property integer  id
 * @property integer  user_id
 * @property integer  product_id
 * @property string   product_data
 * @property DateTime exp_date
 * @property DateTime created_at
 * @property DateTime updated_at
 */
class Subscription extends Model {
    /**
     * @param User $user
     *
     * @return mixed
     */
    public static function findActiveByUser( User $user ) {
        return self::where( [ 'user_id' => $user->id ] )
                   ->where( 'exp_date', '>=', date( 'Y-m-d 00:00:00' ) )
                   ->orderBy( 'created_at', 'desc' )
            ->first();
    }
    public static function getSub($user) {
        return self::where( [ 'user_id' => $user] )->first();
    }
}