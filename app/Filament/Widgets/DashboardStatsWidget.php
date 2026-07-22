<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsWidget extends BaseWidget
{
    // Opcional: Define un orden para que se ubique arriba en el Dashboard
    protected static ?int $sort = 1; 

    protected function getStats(): array
    {
        $today = Carbon::today();

        // 1. Turnos Hoy
        $appointmentsTodayCount = Appointment::whereDate('starts_at', $today)->count();

        // 2. Ingresos Estimados Hoy (Solo Completados)
        $estimatedIncomeToday = Appointment::whereDate('starts_at', $today)
            ->where('status', 'Completado') // Asegúrate de que coincida con el valor exacto de tu BD
            ->sum('final_price');

        // 3. Clientes Atendidos Hoy (Conteo Único)
        $clientsServedTodayCount = Appointment::whereDate('starts_at', $today)
            ->distinct('client_id')
            ->count('client_id');

        return [
            Stat::make('Turnos Hoy', $appointmentsTodayCount)
                ->description('Total de turnos programados para hoy')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Ingresos Estimados Hoy', '$' . number_format($estimatedIncomeToday, 2, ',', '.'))
                ->description('Ingresos de turnos completados')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Clientes Atendidos', $clientsServedTodayCount)
                ->description('Clientes únicos con turnos hoy')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }
}
