<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['user_id', 'name', 'type', 'is_active'];

    //
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class);
    }
}
