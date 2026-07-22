<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use App\Models\Service;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $modelLabel = 'Turno';
    protected static ?string $pluralModelLabel = 'Turnos';
    protected static ?string $navigationLabel = 'Turnos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('client_id')
                    ->relationship('client', 'name')
                    ->label('Cliente')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')->label('Nombre del Cliente')->required(),
                        TextInput::make('phone')->label('Teléfono (Opcional)'),
                    ])
                    ->required(),

                Select::make('service_id')
                    ->relationship('service', 'name')
                    ->label('Servicio')
                    ->searchable()
                    ->preload()
                    ->required(),

                DateTimePicker::make('starts_at')
                    ->label('Día y Hora del Turno')
                    ->native(true) 
                    ->seconds(false)
                    ->required()
                    ->rule(function (Get $get, ?Model $record) {
                        return function (string $attribute, $value, Closure $fail) use ($get, $record) {
                            $serviceId = $get('service_id');
                            
                            if (! $serviceId || ! $value) {
                                return;
                            }

                            $service = Service::find($serviceId);
                            
                            if (! $service) {
                                return;
                            }

                            $startsAt = Carbon::parse($value);
                            $endsAt = $startsAt->copy()->addMinutes($service->duration ?? 0);

                            $overlapping = Appointment::query()
                                ->when($record, fn ($query) => $query->where('id', '!=', $record->id))
                                ->where(function ($query) use ($startsAt, $endsAt) {
                                    $query->where('starts_at', '<', $endsAt)
                                          ->where('ends_at', '>', $startsAt);
                                })
                                ->whereIn('status', ['Pendiente', 'Completado']) 
                                ->exists();

                            if ($overlapping) {
                                $fail('El horario seleccionado se superpone con un turno ya existente.');
                            }
                        };
                    }),

                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'Completado' => 'Completado',
                        'Cancelado' => 'Cancelado',
                    ])
                    ->default('Pendiente')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service.name')
                    ->label('Servicio')
                    ->sortable(),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Inicio')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('final_price')
                    ->label('Precio')
                    ->money('ARS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Completado' => 'success',
                        'Cancelado' => 'danger',
                        'Pendiente' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}