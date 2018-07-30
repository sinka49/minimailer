<?php

namespace App\Modules\Backend\Http\Controllers;


use Illuminate\Http\Request;
use App\Modules\Dashboard\Http\Controllers\Controller as BaseController;
use App\Models\Main;

/**
 * Class AppController
 * @package App\Modules\Dashboard\Http\Controllers
 */
class MainController extends BaseController {
    protected $seo;
    public function __construct()
    {
        $s = Main::find(2);
        $this->seo = json_decode($s->data);
    }
    public function home() {
        $homePage = Main::first();
        $page = json_decode($homePage->data);
        $seo = $this->seo;
        return view( 'backend::app.main', compact('page', 'seo' ));
    }

    public function update(Request $request) {
        $homePage = Main::first();
        $data = (count($homePage))?json_decode($homePage->data):[];
        $item = ['visible'=> $request->input("videoBlock"), 'video'=>$request->input("video")];
        $data->video = $item;
        $item = ['visible'=> $request->input("block1"), 'title'=>$request->input("title1"),'text'=>$request->input("area1")];
        $data->item1 = $item;
        $item = ['visible'=> $request->input("block2"), 'title'=>$request->input("title2"),'text'=>$request->input("area2")];
        $data->item2 = $item;
        $item = ['visible'=> $request->input("block3"), 'title'=>$request->input("title3"),'text'=>$request->input("area3")];
        $data->item3 = $item;
        $item = ['visible'=> $request->input("block4"), 'title'=>$request->input("title4"),'text'=>$request->input("area4")];
        if ($request->file('file4')){
            $img = $request->file('file4');
            $pref = rand(1, 10000);
            $name = $pref.$img->getClientOriginalName();
            $img->move(public_path() . '/images/', $name);
            $item["src"] = "/images/" . $name;

        }
        else{
            $item["src"] = $data->item4->src;
        }
        $data->item4 = $item;
        $item = ['visible'=> $request->input("block5"), 'title'=>$request->input("title5"),'text'=>$request->input("area5")];
        if ($request->file('file5')){
            $img = $request->file('file5');
            $pref = rand(1, 10000);
            $name = $pref.$img->getClientOriginalName();
            $img->move(public_path() . '/images/', $name);
            $item["src"] = "/images/" . $name;

        }
        else{
            $item["src"] = $data->item5->src;
        }
        $data->item5 = $item;
        $item = ['visible'=> $request->input("block6"), 'title'=>$request->input("title6"),'text'=>$request->input("area6")];
        if ($request->file('file6')){
            $img = $request->file('file6');
            $pref = rand(1, 10000);
            $name = $pref.$img->getClientOriginalName();
            $img->move(public_path() . '/images/', $name);
            $item["src"] = "/images/" . $name;

        }
        else{
            $item["src"] = $data->item6->src;
        }
        $data->item6 = $item;
        $item = ['visible'=> $request->input("block7"), 'title'=>$request->input("title7"),'text'=>$request->input("area7"), 'title2'=>$request->input("title71"),'text2'=>$request->input("area71")];
        if ($request->file('file7')){
            $img = $request->file('file7');
            $pref = rand(1, 10000);
            $name = $pref.$img->getClientOriginalName();
            $img->move(public_path() . '/images/', $name);
            $item["src"] = "/images/" . $name;

        }
        else{
            $item["src"] = $data->item7->src;
        }
        $data->item7 = $item;
        $item = ['visible'=> $request->input("block8"), 'title'=>$request->input("title8"),'text'=>$request->input("area8")];
        if ($request->file('file8')){
            $img = $request->file('file8');
            $pref = rand(1, 10000);
            $name = $pref.$img->getClientOriginalName();
            $img->move(public_path() . '/images/', $name);
            $item["src"] = "/images/" . $name;

        }
        else{
            $item["src"] = $data->item8->src;
        }
        $data->item8 = $item;
        $item = ['visible'=> $request->input("block9"), 'title'=>$request->input("title9"),'text'=>$request->input("area9")];
        $data->item9 = $item;
        $item = ['visible'=> $request->input("block10"), 'title'=>$request->input("title10"),'text'=>$request->input("area10")];
        if ($request->file('file10')){
            $img = $request->file('file10');
            $pref = rand(1, 10000);
            $name = $pref.$img->getClientOriginalName();
            $img->move(public_path() . '/images/', $name);
            $item["src"] = "/images/" . $name;

        }
        else{
            $item["src"] = $data->item10->src;
        }
        $data->item10 = $item;
        $item = ['visible'=> $request->input("block11"), 'title'=>$request->input("title11"),'text'=>$request->input("area11")];
        if ($request->file('file11')){
            $img = $request->file('file11');
            $pref = rand(1, 10000);
            $name = $pref.$img->getClientOriginalName();
            $img->move(public_path() . '/images/', $name);
            $item["src"] = "/images/" . $name;

        }
        else{
            $item["src"] = $data->item11->src;
        }
        $data->item11 = $item;
        $homePage->data = json_encode($data);
        $homePage->save();
        return back();
    }


}