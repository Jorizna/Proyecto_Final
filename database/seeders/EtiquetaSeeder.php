<?php

namespace Database\Seeders;

use App\Models\Etiqueta;
use Illuminate\Database\Seeder;

class EtiquetaSeeder extends Seeder
{
    public function run(): void
    {
        $especies = [
            'Trucha común',
            'Trucha arco iris',
            'Carpa',
            'Lucio',
            'Perca',
            'Barbo',
            'Tenca',
            'Cangrejo de río',
            'Black bass',
            'Siluro',
            'Boga',
            'Bermejuela',
            'Madrilla',
            'Cachuelo',
        ];

        foreach ($especies as $especie) {
            Etiqueta::firstOrCreate(['nombre' => $especie]);
        }
    }
}
