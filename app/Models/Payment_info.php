<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment_info extends Model
{
    public static function setPPemail( $id, $email ) {

        $item = Payment_info::where('user_id',$id)->first();
        if (count($item)){
            $item->email_paypal = $email;
            $item->save();
        }
        else{
            $item = new Payment_info();
            $item->user_id = Auth::user()->id;


        $item->email_paypal = $email;
        $item->save();
        }
    }
    public static function setCard($id, $cardNumber ) {

        $item = Payment_info::where('user_id',$id)->first();
        if (count($item)){
            $item->card_number = $cardNumber;
        }
        else{
            $item = new Payment_info();
            $item->user_id = Auth::user()->id;
        }

        $item->card_number = $cardNumber;
        $item->save();
    }

}
