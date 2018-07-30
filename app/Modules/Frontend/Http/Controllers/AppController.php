<?php

namespace App\Modules\Frontend\Http\Controllers;


use App\Models\Product;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

use App\Modules\Dashboard\Http\Controllers\Controller as BaseController;
use Illuminate\Support\Facades\Config;
use App\Models\Main;
/**
 * Class AppController
 * @package App\Modules\Frontend\Http\Controllers
 */
class AppController extends BaseController {
    public function home(Request $request) {
        $affiliateId = $request->input('affiliateId') or null;
        if( $affiliateId ){
           Session::put('affiliateId', $affiliateId);
        }

        $stripe = Config::get('services.stripe');

        $products = Product::all();

        $main = Main::first();
        $page = json_decode($main->data);
        $s = Main::find(2);
        $seo = json_decode($s->data);
        return view('frontend::index', compact('stripe', 'products', 'page', 'seo'), ['affiliateId' => $affiliateId]);
    }


}