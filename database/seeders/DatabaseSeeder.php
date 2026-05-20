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
        User::firstOrCreate(
            ['email' => 'demo@fishspot.local'],
            [
                'name'     => 'Pescador Demo',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );

        // Etiquetas de especies
        $this->call(EtiquetaSeeder::class);
    }
}
