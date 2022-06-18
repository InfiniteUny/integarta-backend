<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use App\Models\Balance;
use App\Models\Transaction;
use App\Models\Institution;
use App\Models\Account;
use App\Models\User;

class BalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Balance::factory()
                ->state(new Sequence(
                    fn ($sequence) => ['user_id' => User::all()->random()]
                ))
                ->has(Account::factory()->count(4)
                    ->state(new Sequence(
                        fn ($sequence) => ['institution_id' => Institution::all()->random()],
                    ))
                    ->state(new Sequence(
                        fn ($sequence) => ['user_id' => User::all()->random()],
                    ))
                    ->has(Transaction::factory()->count(100)
                    )
                )
                ->create();
    }
}
