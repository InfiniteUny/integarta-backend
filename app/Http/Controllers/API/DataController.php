<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Balance;
use App\Models\Account;
use App\Models\Budget;
use App\Models\Target;
use App\Models\Transaction;
use App\Models\Institution;
use Carbon\Carbon;
use Validator;

class DataController extends BaseController
{
    //get all dashboard data
    public function dashboard()
    {
        $data['total_balance'] = Balance::where('user_id', auth()->user()->id)
                            ->where('type', 'main')
                            ->first()->balance;
        $data['income_this_month'] = Transaction::whereRelation('account' ,'user_id', auth()->user()->id)
                            ->where('direction', 'in')
                            ->whereMonth('date', Carbon::now()->month)
                            ->get()->sum('amount');
        $data['expense_this_month'] = Transaction::whereRelation('account' ,'user_id', auth()->user()->id)
                            ->where('direction', 'out')
                            ->whereMonth('date', Carbon::now()->month)
                            ->get()->sum('amount');
        $data['income_last_month'] = Transaction::whereRelation('account' ,'user_id', auth()->user()->id)
                            ->where('direction', 'in')
                            ->whereMonth('date', Carbon::now()->subMonth()->month)
                            ->get()->sum('amount');
        $data['expense_last_month'] = Transaction::whereRelation('account' ,'user_id', auth()->user()->id)
                            ->where('direction', 'out')
                            ->whereMonth('date', Carbon::now()->subMonth()->month)
                            ->get()->sum('amount');
        $data['account'] = Account::where('user_id', auth()->user()->id)->with('institution')->get();
                            
        return $this->sendResponse($data, 'Data fetch successfully.');                       
    }

    // get all transactions data
    public function transactionHistory()
    {
        $data['total_balance'] = Balance::where('user_id', auth()->user()->id)
                            ->where('type', 'main')
                            ->first()->balance;
        $data['transaction_history'] = Transaction::whereRelation('account' ,'user_id', auth()->user()->id)
                            ->with('account')
                            ->orderBy('date', 'desc')
                            ->get();

        return $this->sendResponse($data, 'Data fetch successfully.');    
    }

    // get all connected accounts data
    public function myAccount()
    {
        $data['account'] = Account::where('user_id', auth()->user()->id)->with('institution')->get();
        return $this->sendResponse($data, 'Data fetch successfully.');  
    }

    // get all institutions data
    public function institution()
    {
        $data['institution'] = Institution::all();
        return $this->sendResponse($data, 'Data fetch successfully.');  
    }

    // request otp for new account
    public function requestOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'institution_id' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', ['error'=>$validator->errors()]);
        }

        $endpoint = config('api.brick_url') . 'v1/auth';
        $token = config('api.brick_api_key');

        $institution = Institution::find($request->institution_id);

        if ($institution->type == 'E-Wallet') {

            try {

                $response = Http::withToken($token)->post($endpoint, [
                    'institution_id' => $institution->brick_institution_id,
                    'username' => $request->username
                ]);

                $response = json_decode($response->body());

                return $this->sendResponse($response->data, 'OTP fetch successfully.');  

            } catch (\Throwable $th) {
                return $this->sendError('OTP fetch failed.', ['error'=>'OTP fetch failed']);
            }

        } else {
            
            try {

                $response = Http::withToken($token)->get($endpoint, [
                    'institution_id' => $institution->brick_institution_id,
                    'username' => $request->username,
                    'password' => $request->password
                ]);

                return $this->sendResponse($response, 'OTP fetch successfully.');  

            } catch (\Throwable $th) {
                return $this->sendError('OTP fetch failed.', ['error'=>'OTP fetch failed']);
            }

        }
    }

    // submit otp for new account
    public function submitOtp(Request $request)
    {
        $endpoint = config('api.brick_url') . 'v1/auth/gopay';
        $token = config('api.brick_api_key');

        $institution = Institution::find($request->institution_id);

        if ($institution->brick_institution_id == 11) {

            try {

                $response = Http::withToken($token)->post($endpoint, [
                    'username' => $request->username,
                    'uniqueId' => $request->uniqueId,
                    'sessionId' => $request->sessionId,
                    'otpToken' => $request->otpToken,
                    'otp' => $request->otp,
                ]);

                $response = json_decode($response->body());

                $getBalanceUrl = config('api.brick_url') . 'v1/account/list';
                $getBalance = Http::withToken($response->data)->get($getBalanceUrl);

                $getBalance = json_decode($getBalance->body());

                $balance = $getBalance->data[0]->balances->available;

                $getTransactionUrl = config('api.brick_url') . 'v1/transaction/list?from=2000-01-01&to='. Carbon::now()->format('Y-m-d');
                $getTransaction = Http::withToken($response->data)->get($getTransactionUrl);

                $getTransaction = json_decode($getTransaction->body());

                $transactionHistorys = $getTransaction->data;

                $account = Account::updateOrCreate(
                    ['institution_id' => $institution->id, 'user_id' => auth()->user()->id],
                    ['token' => $response->data, 'balance' => $balance, 'type' => 'main']
                );

                foreach ($transactionHistorys as $transactionHistory) {
                    if (!Transaction::where('reference_id', $transactionHistory->reference_id)->exists()) {
                        $addTransaction = Transaction::Create([
                            'account_id' => $account->id,
                            'amount' => $transactionHistory->amount,
                            'description' => $transactionHistory->description,
                            'direction' => $transactionHistory->direction,
                            'date' => $transactionHistory->date,
                            'category_name' => $transactionHistory->category->category_name,
                            'clasification_group' => $transactionHistory->category->classification_group,
                            'clasification_subgroup' => $transactionHistory->category->classification_subgroup,
                        ]);
                    }
                }

                return $this->sendResponse('ok', 'Account added successfully.');  

            } catch (\Throwable $th) {
                return $this->sendError('Account failed to add.', ['error'=>'Account failed to add']);
            }

        } elseif ($institution->brick_institution_id == 12) {
            
            try {

                $response = Http::withToken($token)->post($endpoint, [
                    'username' => $request->username,
                    'refId' => $request->refId,
                    'deviceId' => $request->deviceId,
                    'pin' => $request->pin,
                    'otpNumber' => $request->otpNumber
                ]);

                $url = config('api.brick_url') . 'v1/account/list';
                $getBalance = Http::withToken($response->data)->get($url);

                $balance = $getBalance->data->balances->available;

                $getTransactionUrl = config('api.brick_url') . 'v1/transaction/list?from=2000-01-01&to='. Carbon::now()->format('Y-m-d');
                $getTransaction = Http::withToken($response->data)->get($getTransactionUrl);

                $transactionHistorys = $getTransaction->data;

                $account = Account::updateOrCreate(
                    ['institution_id' => $institution->id, 'user_id' => auth()->user()->id],
                    ['token' => $response->data, 'balance' => $balance, 'type' => 'main']
                );

                foreach ($transactionHistorys as $transactionHistory) {
                    if (!Transaction::where('reference_id', $transactionHistory->reference_id)->exists()) {
                        $addTransaction = Transaction::Create([
                            'account_id' => $account->id,
                            'amount' => $transactionHistory->amount,
                            'description' => $transactionHistory->description,
                            'direction' => $transactionHistory->direction,
                            'date' => $transactionHistory->date,
                            'category_name' => $transactionHistory->category->category_name,
                            'clasification_group' => $transactionHistory->category->classification_group,
                            'clasification_subgroup' => $transactionHistory->category->classification_subgroup,
                        ]);
                    }
                }

                return $this->sendResponse($response, 'Account added successfully.');  

            } catch (\Throwable $th) {
                return $this->sendError('Account failed to add.', ['error'=>'Account failed to add']);
            }

        }
    }

    // Get budget list
    public function getBudgetList()
    {
        $budget = Budget::select('amount', DB::raw("DATE_FORMAT(date,'%Y-%m') as months"))
                        ->where('user_id', auth()->user()->id)
                        ->orderBy('months', 'desc')
                        ->get();
        $expenses = Transaction::select(
                    DB::raw('sum(amount) as expenses'),
                    DB::raw("DATE_FORMAT(date,'%Y-%m') as months"))
                    ->whereRelation('account' ,'user_id', auth()->user()->id)
                    ->where('direction', 'out')
                    ->groupBy('months')
                    ->orderBy('months', 'desc')
                    ->get();
        
        $data['budget'] = Budget::select('amount', DB::raw("DATE_FORMAT(date,'%Y-%m') as months"))
                                ->where('user_id', auth()->user()->id)
                                ->orderBy('months', 'desc')
                                ->get();
        $data['expenses'] = Transaction::select(
                                DB::raw('sum(amount) as expenses'),
                                DB::raw("DATE_FORMAT(date,'%Y-%m') as months"))
                                ->whereRelation('account' ,'user_id', auth()->user()->id)
                                ->where('direction', 'out')
                                ->groupBy('months')
                                ->orderBy('months', 'desc')
                                ->get();

        return $this->sendResponse($data, 'Data fetch successfully.');
    }
    
    // Get budget list
    public function addBudget(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'month' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', ['error'=>$validator->errors()]);
        }

        $budget = Budget::create([
            'user_id' => auth()->user()->id,
            'amount' => $request->amount,
            'date' => Carbon::parse($request->month)->format('Y-m-d'),
        ]);

        return $this->sendResponse('Ok', 'Data added successfully.');
    }

    // Get target list
    public function getTargetList()
    {
        $data['target'] = Target::where('user_id', auth()->user()->id)
                                ->orderBy('updated_at', 'desc')
                                ->get();

        return $this->sendResponse($data, 'Data fetch successfully.');
    }

    // Edit target list
    public function editDailyTargetPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'target_id' => 'required|string|max:255',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', ['error'=>$validator->errors()]);
        }

        $target = Target::updateOrCreate(
            ['user_id' => auth()->user()->id, 'target_id' => $request->target_id],
            ['daily_payment' => $request->amount]
        );

        return $this->sendResponse('Ok', 'Data updated successfully.');
    }

    // add target list
    public function addTarget(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'expense' => 'required',
            'percentage' => 'required',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', ['error'=>$validator->errors()]);
        }

        $target = Target::create([
            'user_id' => auth()->user()->id,
            'name' => $request->name,
            'expense' => $request->expense,
            'daily_payment' => 0,
            'percentage' => $request->percentage,
            'temp_percentage' => 0,
            'temp_expense' => 0,
        ]);

        return $this->sendResponse('Ok', 'Data created successfully.');
    }
}
