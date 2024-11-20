<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberRequest;
use App\Models\Member;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function create(MemberRequest $request){
        $request->validated();
        $member = Member::create([
            'full_name' => $request->full_name,
            'mobile_number' => $request->mobile_number,
            'telephone_number' => $request->telephone_number,
            'father_name' => $request->father_name,
            'fax' => $request->fax,
            'stock_units' => $request->stock_units,
            'address' => $request->address
        ]);
        if ($member) return response()->json([
            'member' => $member,
            'msg' => 'عضو جدیدی با موفقیت اضافه شد. .',
            'success' => true
        ],201);
        else return response()->json([
            'msg' => 'خطایی در ایجاد عضو رخ داد!',
            'success' => false
        ],500);
    }
    public function update(MemberRequest $request){
        $request->validated();
        $member = Member::where('id',$request->id)->update([
            'full_name' => $request->full_name,
            'mobile_number' => $request->mobile_number,
            'telephone_number' => $request->telephone_number,
            'father_name' => $request->father_name,
            'fax' => $request->fax,
            'stock_units' => $request->stock_units,
            'address' => $request->address,
//            'join_date' => $request->join_date
        ]);
        if ($member) return response()->json([
            'member' => $member,
            'msg' => 'عضو با موفقیت آپدیت شد. .',
            'success' => true
        ],201);
        else return response()->json([
            'msg' => 'خطایی در آپدیت عضو رخ داد!',
            'success' => false
        ],500);
    }
    public function destroy(Request $request){
        return Member::where('id', $request->id)->delete();
//        return response()->json([
//            'member' => $member,
//            'msg' => 'عضو با موفقیت حذف گردید.',
//            'success' => true
//        ]);
    }
    public function showOne($id){
        $member = Member::with(['account'])->where('id', $id)->first();
        if ($member) return response()->json([
            'member' => $member,
             'success' => true
        ]);
        else return response()->json([
            'msg' => 'خطا در پیدا کردن عضو',
            'success' => false
        ]);
    }
    public function showAll(){
        $members = Member::with(['account'])->get();
        return response()->json([
            'members' => $members,
            'success' => true
        ]);
    }
    public function membersList(){
        $members = Member::all();
        return response()->json([
            'members' => $members,
            'success' => true
        ]);
    }
    public function search($str){
        if($str) {
            $members = Member::with(['account'])
                ->when($str != '', function (Builder $q) use ($str) {
                    $q->where('full_name', 'LIKE', "%{$str}%")
                        ->orWhereHas('account', function (Builder $builder) use ($str) {
                            $builder->where('account_number', 'LIKE', "%{$str}%");
                        });
                })->get();
            return response()->json([
                'members' => $members,
                'success' => true
            ]);
        }
    }
    public function updateStocks(Request $request){
        foreach ($request->member_ids as $id){
            Member::where('id',$id)->update(['stock_units' => $request->stock_units]);
        }
        return response()->json([
            'msg' => 'آپدیت با موفقیت انجام شد.',
            'success' => true
        ]);
    }
}
