<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    use HasFactory, Uuids;

    protected $table = 'institution';

    protected $fillable = [
        'brick_institution_id',
        'name',
        'bank_code',
        'logo',
        'type',
    ];

    public function account()
    {
        return $this->hasMany(Account::class);
    }
}
