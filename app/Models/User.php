<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @package App\Models
 * @property integer id
 * @property string  name
 * @property string  email
 * @property string  password
 * @property string  phone
 * @property string  status
 * @property string  role
 * @property string  remember_token
 */
class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function setName($id, $name){
        $user = User::find($id);
        $user->name = $name;
        $user->save();
        return true;
    }
    public static function setPassword($id, $pass){
        $user = User::find($id);
        $user->password = bcrypt($pass);
        $user->save();
        return true;
    }
   
}
