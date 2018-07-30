<?php

namespace App\Modules\Backend\Http\Controllers;


use Illuminate\Http\Request;
use App\Modules\Dashboard\Http\Controllers\Controller as BaseController;
use App\Models\Main;

/**
 * Class AppController
 * @package App\Modules\Dashboard\Http\Controllers
 */
class SeoController extends BaseController {
    protected $seo;
    public function __construct()
    {
        $s = Main::find(2);
        $this->seo = json_decode($s->data);
    }
    public function home() {
        $homePage = Main::find(2);
        $page = json_decode($homePage->data);
        $seo = $this->seo;
        return view( 'backend::app.seo', compact('page' , 'seo') );
    }

    public function update(Request $request) {
        $homePage = Main::find(2);
        $data = ['title'=>$request->input("title"), 'description'=>$request->input("description"),
            'keywords'=>$request->input("keywords"), 'rating'=>$request->input("rating"),
            'robots'=>$request->input("robots"), 'revisit'=>$request->input("revisit"),
            'headGoog'=>$request->input("headGoog"),
            'bodyGoog'=>$request->input("bodyGoog")];
        if ($request->file('fav')){
            $img = $request->file('fav');
            $pref = rand(1, 10000);
            $name = $pref.$img->getClientOriginalName();
            $img->move(public_path() . '/images/', $name);
            $data["favicon"] = "/images/" . $name;

        }
        else{
            $d = json_decode($homePage->data);
            $data["favicon"] = $d->favicon;
        }

        $homePage->data = json_encode($data);
        $homePage->save();
        return back();
    }


}