<?php
/**
 * Created by PhpStorm.
 * User: artem
 * Date: 02/04/2017
 * Time: 23:38
 */

namespace App\Models;

use Illuminate\Support\Facades\Auth as BaseAuth;

class Auth extends BaseAuth{
    /**
     * @return bool
     */
    public static function isCurrentUserAdmin() {
        $u = Auth::user();
        if( !$u ){
            return false;
        }
        return $u->role == 'admin';
    }
}