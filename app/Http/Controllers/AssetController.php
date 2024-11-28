<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetRequest;
use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function create(AssetRequest $request){
        $request->validated();
        $asset = Asset::create([
            'title' => $request->title,
            'cost' => $request->cost,
            'description' => $request->description,
        ]);
        if ($asset) return response()->json([
            'msg' => 'اثاثیه با موفقیت اضافه شد. .',
            'success' => true
        ],201);
        else return response()->json([
            'msg' => 'خطایی در ایجاد اثاثیه رخ داد!',
            'success' => false
        ],500);
    }
    public function update(AssetRequest $request){
        $request->validated();
        if (!$request->id) return response()->json([
            'msg' => 'اثاثیه را انتخاب کنید.'
        ],400);
        $asset = Asset::where('id', $request->id)->update([
            'title' => $request->title,
            'cost' => $request->cost,
            'description' => $request->description,
        ]);

        if ($asset) return response()->json([
            'msg' => 'اثاثیه با موفقیت آپدیت شد. .',
            'success' => true
        ],201);
        else return response()->json([
            'msg' => 'خطایی در آپدیت اثاثیه رخ داد!',
            'success' => false
        ],500);

    }
    public function destroy(Request $request){
        $asset = Asset::where('id', $request->id)->delete();
        return response()->json([
            'msg' => 'اثاثیه با موفقیت حذف گردید.',
            'success' => true
        ]);
    }
    public function showAll(){
        $assets = Asset::all();
        return response()->json([
            'assets' => $assets,
            'success' => true
        ]);
    }
    public function showOne($id){
        $asset = Asset::where('id', $id)->first();
        if ($asset) return response()->json([
            'asset' => $asset,
            'success' => true
        ]);
        else return response()->json([
            'msg' => 'خطا در پیدا کردن اثاثیه',
            'success' => false
        ],500);
    }
}
