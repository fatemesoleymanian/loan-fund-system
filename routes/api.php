<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\FundAccountController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\LoanAccountDetailController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MonthlyChargeController;
use App\Http\Controllers\MonthlyChargeAccountController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
//Route::middleware('token.auth')->group(function (){
Route::get('/test',function (){
    echo 'hii';
});
Route::prefix('/fund_account')->group(function (){
    Route::get('/',[FundAccountController::class,'showAll']);
    Route::get('/{id}',[FundAccountController::class,'showOne']);
    Route::post('/',[FundAccountController::class,'create']);
    Route::put('/',[FundAccountController::class,'update']);
    Route::post('/delete',[FundAccountController::class,'destroy']);
});
Route::prefix('/member')->group(function (){
    Route::get('/',[MemberController::class,'showAll']);
    Route::get('/list',[MemberController::class,'membersList']);
    Route::get('/search={str}',[MemberController::class,'search']);
    Route::get('/{id}',[MemberController::class,'showOne']);
    Route::post('/',[MemberController::class,'create']);
    Route::put('/',[MemberController::class,'update']);
    Route::put('/update_stocks',[MemberController::class,'updateStocks']);
    Route::post('/delete',[MemberController::class,'destroy']);
});
Route::prefix('/account')->group(function (){
    Route::get('/',[AccountController::class,'showAll']);
    Route::get('/list',[AccountController::class,'showList']);
    Route::get('/search={str}',[AccountController::class,'search']);
    Route::get('/{id}',[AccountController::class,'showOne']);
    Route::get('/monthly_charge/{id}',[AccountController::class,'showOneWithMonthlyCharge']);
    Route::post('/',[AccountController::class,'create']);
    Route::put('/',[AccountController::class,'update']);
    Route::post('/delete',[AccountController::class,'destroy']);
});
Route::prefix('/loan')->group(function (){
    Route::get('/',[LoanController::class,'showAll']);
    Route::get('/{id}',[LoanController::class,'showOne']);
    Route::post('/',[LoanController::class,'create']);
    Route::put('/',[LoanController::class,'update']);
    Route::post('/delete',[LoanController::class,'destroy']);
});
Route::prefix('/installment')->group(function (){
    Route::get('/',[InstallmentController::class,'showAll']);
    Route::get('/{id}',[InstallmentController::class,'showOne']);
    Route::post('/',[InstallmentController::class,'create']);
    Route::post('/group',[InstallmentController::class,'createGroup']);
    Route::put('/',[InstallmentController::class,'update']);
    Route::put('/group',[InstallmentController::class,'updateGroup']);
    Route::delete('/',[InstallmentController::class,'destroy']);
    Route::delete('/group',[InstallmentController::class,'destroyGroup']);
});
Route::prefix('/loan_acc_details')->group(function (){
    Route::get('/',[LoanAccountDetailController::class,'showAll']);
    Route::get('/{acc_id}/{loan_id}',[LoanAccountDetailController::class,'showOne']);
    Route::get('/{acc_id}',[LoanAccountDetailController::class,'showOneByAccount']);
    Route::get('/{loan_id}',[LoanAccountDetailController::class,'showOneByLoan']);
    Route::post('/',[LoanAccountDetailController::class,'create']);
    Route::put('/',[LoanAccountDetailController::class,'update']);
    Route::delete('/',[LoanAccountDetailController::class,'destroy']);
});
Route::prefix('/monthly_charge')->group(function (){
    Route::get('/',[MonthlyChargeController::class,'showAll']);
    Route::get('/list',[MonthlyChargeController::class,'showList']);
    Route::get('/{id}',[MonthlyChargeController::class,'showOne']);
    Route::post('/',[MonthlyChargeController::class,'create']);
    Route::put('/',[MonthlyChargeController::class,'update']);
    Route::post('/delete',[MonthlyChargeController::class,'destroy']);
});
Route::prefix('/monthly_charge_member')->group(function (){
    Route::get('/',[MonthlyChargeAccountController::class,'showAll']);
    Route::get('/{charge_id}',[MonthlyChargeAccountController::class,'showAllByCharge']);
    Route::get('/{member_id}',[MonthlyChargeAccountController::class,'showOneByMember']);
    Route::post('/',[MonthlyChargeAccountController::class,'create']);
    Route::put('/',[MonthlyChargeAccountController::class,'update']);
    Route::delete('/',[MonthlyChargeAccountController::class,'destroy']);
});
Route::prefix('/transaction')->group(function (){
        Route::get('/',[TransactionController::class,'showAll']);
        Route::get('/account',[TransactionController::class,'showAllByAccount']);
        Route::get('/type',[TransactionController::class,'showAllByType']);
        Route::get('/acc/{acc_id}/chrg/{charg_id}',[TransactionController::class,'showByAccAndCharge']);
        Route::get('/search',[TransactionController::class,'search']);
        Route::get('/{id}',[TransactionController::class,'showOne']);
        Route::post('/',[TransactionController::class,'create']);
        Route::put('/',[TransactionController::class,'update']);
        Route::delete('/',[TransactionController::class,'destroy']);
    });

//});
