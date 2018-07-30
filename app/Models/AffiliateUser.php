<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth;
use Illuminate\Support\Facades\URL;
use App\Models\User;

/**
 * Class AffiliateUser
 * @package App\Models
 * @property integer  id
 * @property integer  user_id
 * @property string   affiliate_name
 * @property boolean  accepted_rules
 * @property integer  parent_id
 * @property float    total_income
 * @property DateTime created_at
 * @property DateTime updated_at
 */
class AffiliateUser extends Model {

    /**
     * @return bool
     */
    public static function isRulesAccepted() {
        $affiliateUser = self::findByCurrentUser();

        if ( ! $affiliateUser ) {
            return false;
        }

        return $affiliateUser->accepted_rules;
    }

    /**
     * @return AffiliateUser|null
     */
    public static function findByCurrentUser() {
        $user = Auth::user();
        if ( ! $user ) {
            return false;
        }
        $affiliateUser = self::where( [ 'user_id' => $user->id ] )
                             ->first();

        if ( !$affiliateUser ) {
            return null;
        }

        return $affiliateUser;
    }
    public static function findByUser($id) {

        $affiliateUser = self::where( [ 'user_id' => $id ] )
            ->first();

        if ( !$affiliateUser ) {
            return null;
        }

        return $affiliateUser;
    }
    /**
     * @param \App\Models\User      $user
     * @param \App\Models\User|null $parent
     * @param string                $affiliateName
     * @param int                   $acceptedRules
     * @param float                 $totalIncome
     *
     * @return bool
     */
    public static function make( User $user, User $parent = null, $affiliateName = '', $acceptedRules = 0, $totalIncome = 0.0 ) {
        $au                 = new AffiliateUser();

        if( !$affiliateName ){
            $affiliateName = stristr($user->email, '@', true);
        }

        $au->user_id        = $user->id;
        $au->parent_id      = null;
        if( $parent ){
            $au->parent_id      = $parent->id;
        }

        $au->accepted_rules = $acceptedRules;
        $au->affiliate_name = $affiliateName;
        $au->total_income   = $totalIncome;

        return $au->save();
    }

    /**
     * @param $parentId integer
     *
     * @return mixed AffiliateUser
     */
    public static function findByParentId( $parentId ) {
        return AffiliateUser::where( [ 'parent_id' => $parentId ] )->get();
    }



    /**
     * @return string
     */
    public static function getAffiliateUrl() {
        $affiliateUser = self::findByCurrentUser();


        if ( $affiliateUser ) {
            $affiliateName = $affiliateUser->affiliate_name;
            return URL::to( "/?affiliateId=$affiliateName" );
        }
        else return false;

    }

    /**
     * @return string
     */
    public function getAffiliateName() {
        return $this->affiliate_name;
    }
}
