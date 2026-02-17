<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $fillable = ['user_id','date','amount','from_account_id','to_account_id','description'];

    //
    public function user(){
        return $this->belongsTo(\App\Models\User::class);
    }

    public function fromAccount(){
        return $this->belongsTo(\App\Models\Account::class, 'from_account_id');
    }

    public function toAccount(){
        return $this->belongsTo(\App\Models\Account::class, 'to_account_id');
    }
}
