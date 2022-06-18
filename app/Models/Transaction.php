<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, Uuids;
    
    protected $table = 'transaction';

    protected $fillable = [
        'account_id',
        'amount',
        'description',
        'direction',
        'date',
        'category_name',
        'classification_group',
        'classification_subgroup',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
