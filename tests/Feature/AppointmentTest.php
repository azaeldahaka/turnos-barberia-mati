<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that saving an appointment automatically calculates the end time and final price.
     */
    public function test_appointment_calculates_ends_at_and_final_price_correctly(): void
    {
        // 1. Crear un cliente
        $client = Client::create([
            'name' => 'Cliente Test',
            'phone' => '123456789',
            'email' => 'cliente@test.com',
            'affiliate_points' => 0,
        ]);

        // 2. Crear un servicio con precio base y duración específica
        $service = Service::create([
            'name' => 'Corte Moderno',
            'base_price' => 8500.00,
            'duration_minutes' => 45,
        ]);

        // 3. Crear el turno con hora de inicio
        $appointment = Appointment::create([
            'client_id' => $client->id,
            'service_id' => $service->id,
            'starts_at' => '2026-07-22 10:00:00',
        ]);

        // 4. Verificar que se haya autocalculado el precio final y la hora de fin
        $this->assertEquals(8500.00, $appointment->final_price);
        $this->assertEquals('2026-07-22 10:45:00', $appointment->ends_at->format('Y-m-d H:i:s'));
    }

    /**
     * Test that if a final price is already set, it is not overridden.
     */
    public function test_appointment_does_not_override_predefined_final_price(): void
    {
        $client = Client::create([
            'name' => 'Cliente VIP',
        ]);

        $service = Service::create([
            'name' => 'Corte Premium',
            'base_price' => 12000.00,
            'duration_minutes' => 60,
        ]);

        // Creamos el turno indicando un precio especial rebajado
        $appointment = Appointment::create([
            'client_id' => $client->id,
            'service_id' => $service->id,
            'starts_at' => '2026-07-22 12:00:00',
            'final_price' => 9500.00, // Precio con descuento manual
        ]);

        // Verificamos que se haya calculado el tiempo pero respetado el precio final manual
        $this->assertEquals(9500.00, $appointment->final_price);
        $this->assertEquals('2026-07-22 13:00:00', $appointment->ends_at->format('Y-m-d H:i:s'));
    }
}
