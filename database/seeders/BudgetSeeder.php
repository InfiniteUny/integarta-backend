<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Budget;
use App\Models\Balance;

class BudgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Budget::factory()->count(2)
                ->state(new Sequence(
                    fn ($sequence) => ['user_id' => User::all()->random()]
                ))
                ->create();
    }
}
