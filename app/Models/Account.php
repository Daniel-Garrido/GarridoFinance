<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Account extends Model
{

    use HasFactory;

    protected $fillable = ['user_id', 'name', 'type', 'is_active'];

    // Tipos de cuenta: etiqueta, descripción corta y tono de color (paleta del mockup)
    public const TYPES = [
        'cash' => ['label' => 'Efectivo', 'subtitle' => 'Dinero en efectivo', 'tone' => 'income'],
        'bank' => ['label' => 'Banco', 'subtitle' => 'Cuenta bancaria', 'tone' => 'primary'],
        'card' => ['label' => 'Tarjeta', 'subtitle' => 'Tarjeta de pago', 'tone' => 'expense'],
        'saving' => ['label' => 'Ahorro', 'subtitle' => 'Cuenta de ahorro', 'tone' => 'info'],
    ];

    // Datos del tipo de la cuenta; si el tipo no está en la lista se muestra tal cual
    public function getTypeInfoAttribute(): array
    {
        return self::TYPES[$this->type] ?? [
            'label' => ucfirst($this->type),
            'subtitle' => 'Cuenta financiera',
            'tone' => 'primary',
        ];
    }

    // Accessor para calcular el saldo de la cuenta
    public function getBalanceAttribute()
    {
        return ($this->income_sum ?? 0)
            - ($this->expense_sum ?? 0)
            + ($this->transfers_in_sum ?? 0)
            - ($this->transfers_out_sum ?? 0);
    }

    //
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class);
    }

    public function outgoingTransfers()
    {
        return $this->hasMany(\App\Models\Transfer::class, 'from_account_id');
    }

    public function incomingTransfers()
    {
        return $this->hasMany(\App\Models\Transfer::class, 'to_account_id');
    }
}
