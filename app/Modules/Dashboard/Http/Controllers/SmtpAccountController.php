<?php

namespace App\Modules\Dashboard\Http\Controllers;

use App\Models\SmtpAccount;
use Illuminate\Http\Request;

use App\Models\Auth;
use App\Http\Requests;
use Illuminate\Http\Response;

class SmtpAccountController extends Controller
{
    /**
     * @return mixed
     */
    public function getLst() {
        $smtpAccounts = SmtpAccount::findAllWithUser( Auth::user() );
        return $smtpAccounts->toJson();
    }

    /**
     * Responds to requests to GET /smtp-accounts/1
     */
    public function show($id)
    {
        $accounts = SmtpAccount::findOneWithUser($id, Auth::user());

        if( !$accounts ){
            return [];
        }

        return $accounts->toJson();
    }

    /**
     * Responds to requests to GET /smtp-accounts
     *
     * @return array
     */
    public function index()
    {
        $accounts = SmtpAccount::findAllWithUser(Auth::user());

        if( !$accounts ){
            return [];
        }

        return $accounts->toJson();
    }
}
