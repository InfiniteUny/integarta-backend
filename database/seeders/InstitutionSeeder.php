<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Institution;

class InstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $institution = Institution::create([
            'id' => Str::uuid()->toString(),
            'brick_institution_id' => 2,
            'name' => 'KlikBCA Internet Banking',
            'bank_code' => 'KlikBCA Internet Banking',
            'logo' => 'bca.png',
            'type' => 'Internet Banking',
        ]);
        $institution = Institution::create([
            'id' => Str::uuid()->toString(),
            'brick_institution_id' => 5,
            'name' => 'BRI Internet Banking',
            'bank_code' => 'BRI Internet Banking',
            'logo' => 'bri.png',
            'type' => 'Internet Banking',
        ]);
        $institution = Institution::create([
            'id' => Str::uuid()->toString(),
            'brick_institution_id' => 11,
            'name' => 'GoPay',
            'bank_code' => 'GoPay',
            'logo' => 'gopay.png',
            'type' => 'E-Wallet',
        ]);
        $institution = Institution::create([
            'id' => Str::uuid()->toString(),
            'brick_institution_id' => 12,
            'name' => 'OVO',
            'bank_code' => 'OVO',
            'logo' => 'ovo.png',
            'type' => 'E-Wallet',
        ]);
    }
}
