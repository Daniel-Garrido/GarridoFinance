<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['user_id', 'name', 'type', 'is_active'];

    // Tipos de categoría: etiqueta, nombre del filtro, descripción corta y tono de color (paleta del mockup)
    public const TYPES = [
        'income' => ['label' => 'Ingreso', 'filter' => 'Ingresos', 'subtitle' => 'Categoría de ingreso', 'tone' => 'income'],
        'expense' => ['label' => 'Gasto', 'filter' => 'Gastos', 'subtitle' => 'Categoría de gasto', 'tone' => 'expense'],
    ];

    // Datos del tipo de la categoría
    public function getTypeInfoAttribute(): array
    {
        return self::TYPES[$this->type] ?? self::TYPES['expense'];
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
}
