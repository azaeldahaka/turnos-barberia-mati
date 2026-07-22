<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class TurnosCalendar extends FullCalendarWidget
{
    public function config(): array
    {
        return [
            'headerToolbar' => [
                'left' => 'prev,next',
                'center' => 'title',
                'right' => 'timeGridDay,timeGridWeek,dayGridMonth,listWeek'
            ],
            'initialView' => 'timeGridDay',
            'height' => 'auto',
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        // El 'with' hace que cargue los nombres de cliente y servicio de forma súper rápida
        return Appointment::query()
            ->with(['client', 'service']) 
            ->where('starts_at', '>=', $fetchInfo['start'])
            ->where('ends_at', '<=', $fetchInfo['end'])
            ->get()
            ->map(function (Appointment $appointment) {
                
                // Elegimos el color del cuadradito según el estado del turno
                $color = match ($appointment->status) {
                    'Completado' => '#10B981', // Verde
                    'Cancelado' => '#EF4444', // Rojo
                    default => '#3B82F6', // Azul (Pendiente)
                };

                return [
                    'id' => $appointment->id,
                    // Ahora el título va a decir "Facundo - Corte + Barba"
                    'title' => $appointment->client->name . ' - ' . $appointment->service->name,
                    'start' => $appointment->starts_at,
                    'end' => $appointment->ends_at,
                    'color' => $color,
                    // Magia extra: si hacés clic en el turno en el calendario, te lleva a editarlo
                    'url' => \App\Filament\Resources\AppointmentResource::getUrl('edit', ['record' => $appointment]),
                ];
            })
            ->all();
    }
}