<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

///add amount ///
      public function credit(Request $request){

            $data = $request->all();
            $validator = Validator::make($data, [
                    'user_id' => 'required|max:255',
                    'amount' => 'required'
            ]);
            if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }
            if($request->amount < 3){
            return response(['amount' => 'The amount value should between 3 and 100',
            ]); 
        }
            if($request->amount > 100){
            return response(['amount' => 'The amount value should between 3 and 100',
              ]);    
        }

        try {
        $wallet = wallet::create([
            "user_id" => $request->user_id,
            "amount" => $request->amount,
            "type" => 'credit',
        ]);

       $credit = DB::table('wallets')->where('type', 'credit')->where('user_id', $request->user_id)->sum('amount');
        $debit = DB::table('wallets')->where('type', 'debit')->where('user_id', $request->user_id)->sum('amount');
        $amount = $credit - $debit;
        
    } catch (wallet $exception)   {
        return [
            "status" => 'false',
            "balance" => [],
            "msg" => "Something went wrong"
        ];
    }
        return [
                    "status" => 'true',
                    "balance" => $amount,
                    "msg" => "credit amount successfully"
                ];
           
    }

        ///debit ///
        public function debit(Request $request){
            $data = $request->all();
            $validator = Validator::make($data, [
                    'user_id' => 'required|max:255',
                    'cookie' => 'required'
                ]);
            if ($validator->fails()) {
                return response(['error' => $validator->errors(), 'Validation Error']);
            }
       
     try{
        $credit = DB::table('wallets')->where('type', 'credit')->where('user_id', $request->user_id)->sum('amount');
        $debit = DB::table('wallets')->where('type', 'debit')->where('user_id', $request->user_id)->sum('amount');
        $amount = $credit - $debit;
        if($amount > 0){
        $wallet = wallet::create([
            "user_id" => $request->user_id,
            "amount" => $request->cookie,
            "type" => 'debit',
        ]);
        return [
                    "status" => 'true',
                    "balance" => $amount-$request->cookie,
                    "msg" => "cookie buy successfully"
                ];
            }else{
                return [
                    "status" => 'true',
                    "balance" => $amount,
                    "msg" => "You dont have sufficent fund"
                ];
            }
     } catch (wallet $exception)   {
            return [
                "status" => 'false',
                "balance" => [],
                "msg" => "Something went wrong"
            ];

    }
        
}



}
