<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestPayment extends Model
{
    public static function getPayment($id)
    {
        $payment = GuestPayment::where("userPayment", $id)->first();
        return (count($payment)>0)? $payment : false;
    }
    public static function setUser(User $user)
    {
        $his->user_id = GuestPayment::where("userPayment", $id)->first();
        return (count($payment)>0)? $payment : false;
    }

}
