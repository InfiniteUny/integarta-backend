<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Balance;
use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;

class DataController extends BaseController
{
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

    public function myAccount()
    {
        $data['account'] = Account::where('user_id', auth()->user()->id)->with('institution')->get();
        return $this->sendResponse($data, 'Data fetch successfully.');  
    }
}
