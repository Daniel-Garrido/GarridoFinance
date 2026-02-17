<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = ['user_id', 'name', 'type', 'is_active'];
    
    //
    public function user(){
        return $this->belongsTo(\App\Models\User::class);
    }

    public function transactions(){
        return $this->hasMany(\App\Models\Transaction::class);
    }

    public function outgoingTransfers(){
        return $this->hasMany(\App\Models\Transfer::class, 'from_account_id');
    }

    public function incomingTransfers(){
        return $this->hasMany(\App\Models\Transfer::class, 'to_account_id');
    }
}
