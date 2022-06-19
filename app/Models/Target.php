<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    use HasFactory, Uuids;

    protected $table = 'target';

    protected $fillable = [
        'user_id',
        'name',
        'expense',
        'daily_payment',
        'percentage',
        'temp_percentage',
        'temp_expense',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
