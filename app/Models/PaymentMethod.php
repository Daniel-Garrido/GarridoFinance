<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentMethod extends Model
{
    protected $fillable = ['user_id', 'name', 'is_active'];

    // Clases de método de pago (paleta del mockup). La palabra clave se busca en el nombre, sin acentos
    public const KINDS = [
        'credito' => ['subtitle' => 'Tarjeta de crédito', 'tone' => 'expense'],
        'debito' => ['subtitle' => 'Tarjeta de débito', 'tone' => 'primary'],
        'efectivo' => ['subtitle' => 'Pago en efectivo', 'tone' => 'income'],
        'transferencia' => ['subtitle' => 'Transferencia bancaria', 'tone' => 'info'],
    ];

    // Clase deducida del nombre; si no coincide con ninguna, se muestra como método de pago genérico
    public function getKindInfoAttribute(): array
    {
        $name = Str::lower(Str::ascii($this->name));

        foreach (self::KINDS as $keyword => $info) {
            if (Str::contains($name, $keyword)) {
                return $info;
            }
        }

        return ['subtitle' => 'Método de pago', 'tone' => 'primary'];
    }

    //
    public function user(){
        return $this->belongsTo(\App\Models\User::class);
    }

    public function transactions(){
        return $this->hasMany(\App\Models\Transaction::class);
    }
}
