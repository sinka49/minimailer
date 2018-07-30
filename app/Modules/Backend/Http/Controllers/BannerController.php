<?php

namespace App\Modules\Backend\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Modules\Dashboard\Http\Controllers\Controller as BaseController;
use App\Models\Banner;
use App\Models\Main;
/**
 * Class AppController
 * @package App\Modules\Dashboard\Http\Controllers
 */
class BannerController extends BaseController {
    protected $seo;
    public function __construct()
    {
        $s = Main::find(2);
        $this->seo = json_decode($s->data);
    }
    public function home() {
        $items= Banner::all();
        return view( 'backend::app.banners', compact('items' , 'seo') );
    }

    public function add(Request $request) {
       $banner = new Banner();
        if ($request->file('fav')){
            $img = $request->file('fav');
            $pref = rand(1, 10000);
            $name = $pref.$img->getClientOriginalName();
            $img->move(public_path() . '/images/', $name);
            $banner->src = "/images/" . $name;
            $banner->save();
        }
        return back();
    }
    public function remove($id) {

        if ($id){
            $banner = Banner::find($id);
            $f = Storage::disk('local');
            $f->delete($banner->src);
            Banner::destroy($id);
        }
        return back();
    }


}