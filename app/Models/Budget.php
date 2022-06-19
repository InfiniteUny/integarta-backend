<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory, Uuids;

    protected $table = 'budget';

    protected $fillable = [
        'user_id',
        'name',
        'date',
        'amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
