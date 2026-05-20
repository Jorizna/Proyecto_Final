<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Usuario de prueba
        User::factory()->create([
            'name'  => 'Pescador Demo',
            'email' => 'demo@fishspot.local',
        ]);

        // Etiquetas de especies
        $this->call(EtiquetaSeeder::class);
    }
}
