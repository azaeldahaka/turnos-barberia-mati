<?php

namespace Tests\Feature;

use App\Filament\Resources\AppointmentResource\Pages\CreateAppointment;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AppointmentOverlapTest extends TestCase
{
    use RefreshDatabase;

    private function createClient()
    {
        return Client::forceCreate([
            'name' => 'Cliente de Prueba',
            'phone' => '123456789',
        ]);
    }

    private function createService(int $duration = 30)
    {
        return Service::forceCreate([
            'name' => 'Corte de Prueba',
            'price' => 1500,
            'duration' => $duration,
        ]);
    }

    private function createAppointment($client, $service, $startsAt, $endsAt, $status = 'Pendiente')
    {
        return Appointment::forceCreate([
            'client_id' => $client->id,
            'service_id' => $service->id,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'final_price' => $service->price,
            'status' => $status,
        ]);
    }

    public function test_camino_feliz_permite_agendar_un_turno_en_un_horario_libre()
    {
        $client = $this->createClient();
        $service = $this->createService(30);

        Livewire::test(CreateAppointment::class)
            ->fillForm([
                'client_id' => $client->id,
                'service_id' => $service->id,
                'starts_at' => Carbon::tomorrow()->setHour(10)->setMinute(0)->toDateTimeString(),
                'status' => 'Pendiente',
            ])
            ->call('create')
            ->assertHasNoFormErrors();
    }

    public function test_rechaza_superposicion_exacta()
    {
        $client = $this->createClient();
        $service = $this->createService(30);
        $startTime = Carbon::tomorrow()->setHour(10)->setMinute(0);

        $this->createAppointment(
            $client, 
            $service, 
            $startTime, 
            $startTime->copy()->addMinutes(30)
        );

        Livewire::test(CreateAppointment::class)
            ->fillForm([
                'client_id' => $client->id,
                'service_id' => $service->id,
                'starts_at' => $startTime->toDateTimeString(),
                'status' => 'Pendiente',
            ])
            ->call('create')
            ->assertHasFormErrors(['starts_at']);
    }

    public function test_rechaza_superposicion_parcial()
    {
        $client = $this->createClient();
        $service = $this->createService(30);
        $startTime = Carbon::tomorrow()->setHour(10)->setMinute(0);

        $this->createAppointment(
            $client, 
            $service, 
            $startTime, 
            $startTime->copy()->addMinutes(30)
        );

        Livewire::test(CreateAppointment::class)
            ->fillForm([
                'client_id' => $client->id,
                'service_id' => $service->id,
                'starts_at' => $startTime->copy()->addMinutes(15)->toDateTimeString(),
                'status' => 'Pendiente',
            ])
            ->call('create')
            ->assertHasFormErrors(['starts_at']);
    }

    public function test_rechaza_superposicion_envolvente()
    {
        $client = $this->createClient();
        $service = $this->createService(60);
        
        $existingStartTime = Carbon::tomorrow()->setHour(10)->setMinute(15);
        $this->createAppointment(
            $client, 
            $service, 
            $existingStartTime, 
            $existingStartTime->copy()->addMinutes(15)
        );

        $newStartTime = Carbon::tomorrow()->setHour(10)->setMinute(0);

        Livewire::test(CreateAppointment::class)
            ->fillForm([
                'client_id' => $client->id,
                'service_id' => $service->id,
                'starts_at' => $newStartTime->toDateTimeString(),
                'status' => 'Pendiente',
            ])
            ->call('create')
            ->assertHasFormErrors(['starts_at']);
    }

    public function test_limite_permitido()
    {
        $client = $this->createClient();
        $service = $this->createService(30);
        $startTime = Carbon::tomorrow()->setHour(10)->setMinute(0);

        $this->createAppointment(
            $client, 
            $service, 
            $startTime, 
            $startTime->copy()->addMinutes(30)
        );

        Livewire::test(CreateAppointment::class)
            ->fillForm([
                'client_id' => $client->id,
                'service_id' => $service->id,
                'starts_at' => $startTime->copy()->addMinutes(30)->toDateTimeString(),
                'status' => 'Pendiente',
            ])
            ->call('create')
            ->assertHasNoFormErrors();
    }
}
