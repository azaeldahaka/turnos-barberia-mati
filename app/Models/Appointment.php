<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $guarded = []; 

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'final_price' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Appointment $appointment) {
            // Si el turno tiene un servicio y una hora de inicio...
            if ($appointment->service_id && $appointment->starts_at) {
                // Buscamos el servicio de forma óptima
                $service = ($appointment->isDirty('service_id') || !$appointment->relationLoaded('service'))
                    ? \App\Models\Service::find($appointment->service_id)
                    : $appointment->service;

                if ($service) {
                    // 1. Calculamos y guardamos la hora de fin sumando la duración en minutos
                    $duration = (int) ($service->duration_minutes ?? 30);
                    $appointment->ends_at = \Carbon\Carbon::parse($appointment->starts_at)->addMinutes($duration);

                    // 2. Traemos el precio base si no se ha asignado un precio final previamente
                    if (blank($appointment->final_price) || (float) $appointment->final_price === 0.0) {
                        $appointment->final_price = $service->base_price ?? 0;
                    }
                }
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}