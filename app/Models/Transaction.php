<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'type',
        'amount',
        'account_id',
        'category_id',
        'payment_method_id',
        'description',
        'is_historical'
    ];

    //s
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(\App\Models\PaymentMethod::class);
    }
}
