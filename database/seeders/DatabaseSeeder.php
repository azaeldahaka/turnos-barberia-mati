<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crear el usuario administrador de prueba si no existe
        User::firstOrCreate(
            ['email' => 'admin@barberia.com'],
            [
                'name' => 'Barbero Admin',
                'password' => bcrypt('facundoflores1323'),
            ]
        );

        // 2. Crear los servicios típicos de una barbería
        $servicios = [
            [
                'name' => 'Corte de Cabello Clásico',
                'base_price' => 7000.00,
                'duration_minutes' => 30,
            ],
            [
                'name' => 'Perfilado y Corte de Barba',
                'base_price' => 5000.00,
                'duration_minutes' => 20,
            ],
            [
                'name' => 'Combo: Corte + Barba',
                'base_price' => 10000.00,
                'duration_minutes' => 45,
            ],
            [
                'name' => 'Lavado y Peinado',
                'base_price' => 3000.00,
                'duration_minutes' => 15,
            ],
        ];

        foreach ($servicios as $servicio) {
            \App\Models\Service::firstOrCreate(['name' => $servicio['name']], $servicio);
        }

        // 3. Crear clientes recurrentes de prueba
        $clientes = [
            [
                'name' => 'Juan Pérez',
                'phone' => '1122334455',
                'email' => 'juan.perez@email.com',
                'affiliate_points' => 10,
            ],
            [
                'name' => 'Martín Rodríguez',
                'phone' => '1133445566',
                'email' => 'martin.rod@email.com',
                'affiliate_points' => 5,
            ],
            [
                'name' => 'Diego Gómez',
                'phone' => '1155667788',
                'email' => 'diego.gomez@email.com',
                'affiliate_points' => 25,
            ],
        ];

        foreach ($clientes as $cliente) {
            \App\Models\Client::firstOrCreate(['name' => $cliente['name']], $cliente);
        }
    }
}
