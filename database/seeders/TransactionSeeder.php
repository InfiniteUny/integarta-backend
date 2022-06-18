<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\Account;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Transaction::factory()->count(100)
                ->state(new Sequence(
                    fn ($sequence) => ['account_id' => Account::all()->random()]
                ))
                ->create();
    }
}
