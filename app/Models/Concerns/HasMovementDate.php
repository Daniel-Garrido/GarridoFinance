<?php

namespace App\Models\Concerns;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Comportamiento compartido por los movimientos con columna "date"
 * (transacciones y transferencias): etiquetas de fecha y filtro por periodo.
 */
trait HasMovementDate
{
    // Fecha corta en español, ej. "15 sep 2026"
    public function getDateLabelAttribute(): string
    {
        return str_replace('.', '', $this->date->locale('es')->translatedFormat('j M Y'));
    }

    // Segunda línea de la fecha: "Hoy", "Ayer" o el día de la semana
    public function getDateHintAttribute(): string
    {
        if ($this->date->isToday()) {
            return 'Hoy';
        }

        if ($this->date->isYesterday()) {
            return 'Ayer';
        }

        return ucfirst($this->date->locale('es')->translatedFormat('l'));
    }

    // Filtro por periodo (day | week | month | year) con el valor de su input;
    // si el valor llega con un formato inválido, el filtro se ignora
    public function scopeInPeriod(Builder $query, Request $request): Builder
    {
        switch ($request->period) {
            case 'day':
                // formato recibido del input type="date": "2026-07-15"
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $request->date)) {
                    $query->whereDate('date', $request->date);
                }
                break;

            case 'week':
                // formato recibido del input type="week": "2026-W27"
                if (preg_match('/^(\d{4})-W(\d{2})$/', (string) $request->week, $m)) {
                    $start = Carbon::now()->setISODate((int) $m[1], (int) $m[2])->startOfWeek();
                    $query->whereBetween('date', [$start->toDateString(), $start->copy()->endOfWeek()->toDateString()]);
                }
                break;

            case 'month':
                // formato recibido del input type="month": "2026-07"
                if (preg_match('/^(\d{4})-(\d{2})$/', (string) $request->month, $m)) {
                    $query->whereYear('date', $m[1])->whereMonth('date', $m[2]);
                }
                break;

            case 'year':
                if (preg_match('/^\d{4}$/', (string) $request->year)) {
                    $query->whereYear('date', $request->year);
                }
                break;
        }

        return $query;
    }
}
