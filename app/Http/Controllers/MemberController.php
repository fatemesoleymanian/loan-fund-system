<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberRequest;
use App\Models\Account;
use App\Models\Member;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    public function create($request){
        $request->validated();
        $member = Member::create([
            'full_name' => $request->full_name,
            'mobile_number' => $request->mobile_number,
            'telephone_number' => $request->telephone_number,
            'father_name' => $request->father_name,
            'fax' => $request->fax,
            'address' => $request->address
        ]);
        return $member;
//        if ($member) return response()->json([
//            'member' => $member,
//            'msg' => 'عضو جدیدی با موفقیت اضافه شد. .',
//            'success' => true
//        ],201);
//        else return response()->json([
//            'msg' => 'خطایی در ایجاد عضو رخ داد!',
//            'success' => false
//        ],500);
    }
    public function update(MemberRequest $request){
        DB::beginTransaction();
        try{
            $request->validated();
            $account = Account::withoutGlobalScope('is_open')->where('id', $request->account_id)->update([
                'member_name' => $request->full_name,
            ]);
            $member = Member::where('id', $request->id)->update([
                'full_name' => $request->full_name,
                'mobile_number' => $request->mobile_number,
                'telephone_number' => $request->telephone_number,
                'father_name' => $request->father_name,
                'fax' => $request->fax,
                'address' => $request->address,
            ]);
            DB::commit();
            if ($member) return response()->json([
                'account'=>$account,
                'member' => $member,
                'msg' => 'عضو با موفقیت آپدیت شد. .',
                'success' => true
            ], 201);
            else return response()->json([
                'msg' => 'خطایی در آپدیت عضو رخ داد!',
                'success' => false
            ], 500);
        }catch (\Exception $e) {
            DB::rollBack();
            return TransactionController::errorResponse('خطایی در آپدیت رخ داد! ' . $e->getMessage());
        }
    }
    public function updateWithAccountUpdate($request){
        $member = Member::where('id',$request->member_id)->update([
            'full_name' => $request->full_name,
            'mobile_number' => $request->mobile_number,
            'telephone_number' => $request->telephone_number,
            'father_name' => $request->father_name,
            'fax' => $request->fax,
            'address' => $request->address,
        ]);
        if ($member) return $member;
        else return response()->json([
            'msg' => 'خطایی در آپدیت عضو رخ داد!',
            'success' => false
        ],500);
    }

//    public function destroy(Request $request){
//        return Member::where('id', $request->id)->delete();
////        return response()->json([
//            'member' => $member,
//            'msg' => 'عضو با موفقیت حذف گردید.',
//            'success' => true
//        ]);
//    }
    public function showOne($id){
        $member = Member::whereHas('account',function ($query){
            $query->where('is_open',true);
        })->with(['account'])->where('id', $id)->first();
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
//        $members = Member::whereHas('account',function ($query){
//            $query->where('is_open',true);
//        })->with(['account'])->get();
        $members = Member::with([
            'account' => function($query){
            $query->withoutGlobalScope('is_open');
            }
        ])->get();
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
            $members = Member::when($str != '', function (Builder $q) use ($str) {
                    $q->where('full_name', 'LIKE', "%{$str}%");
                })->get();
            return response()->json([
                'members' => $members,
                'success' => true
            ]);
        }
    }

}
