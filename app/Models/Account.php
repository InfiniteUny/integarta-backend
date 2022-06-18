<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory, Uuids;
    
    protected $table = 'account';

    protected $fillable = [
        'institution_id',
        'user_id',
        'balance_id',
        'balance',
        'token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function balance()
    {
        return $this->belongsTo(Balance::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }
}
