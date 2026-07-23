<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class TurnosCalendar extends FullCalendarWidget
{
    protected static string $view = 'filament.widgets.turnos-calendar';

    public function config(): array
    {
        return [
            'headerToolbar' => false,
            'initialView' => 'timeGridWeek',
            'height' => 'auto',
            'slotMinTime' => '09:00:00',
            'slotMaxTime' => '21:00:00',
            'nowIndicator' => true,
            'allDaySlot' => false,
            'slotLabelFormat' => [
                'hour' => 'numeric',
                'meridiem' => 'short'
            ],
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        return Appointment::query()
            ->with(['client', 'service']) 
            ->where('starts_at', '>=', $fetchInfo['start'])
            ->where('ends_at', '<=', $fetchInfo['end'])
            ->get()
            ->map(function (Appointment $appointment) {
                
                // Paleta de colores "Pastel Premium"
                $pastelColors = [
                    '#93C5FD', // Azul suave (Corte)
                    '#6EE7B7', // Verde esmeralda (Barba)
                    '#FCD34D', // Ámbar (Tratamientos)
                    '#C4B5FD', // Violeta
                    '#F9A8D4', // Rosa pastel
                ];
                
                // Asignar color dinámico basado en el ID del servicio de forma predecible
                $colorIndex = $appointment->service_id % count($pastelColors);
                $serviceColor = $pastelColors[$colorIndex];

                // Si está cancelado, podemos forzar un rojo pálido o mantener la lógica
                $bgColor = match ($appointment->status) {
                    'Cancelado' => '#FCA5A5', // Rojo pastel
                    'Completado' => '#D1FAE5', // Verde muy claro
                    default => $serviceColor,
                };

                return [
                    'id' => $appointment->id,
                    'title' => $appointment->client->name . ' - ' . $appointment->service->name,
                    'start' => $appointment->starts_at,
                    'end' => $appointment->ends_at,
                    'backgroundColor' => $bgColor,
                    'borderColor' => 'transparent', // Sin bordes
                    'classNames' => [
                        'rounded-lg', 
                        'border-none', 
                        'shadow-sm', 
                        'font-medium', 
                        'text-gray-800' // Texto legible para colores pasteles
                    ],
                    'url' => \App\Filament\Resources\AppointmentResource::getUrl('edit', ['record' => $appointment]),
                ];
            })
            ->all();
    }
}