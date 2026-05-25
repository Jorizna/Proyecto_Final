<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publicaciones', function (Blueprint $table) {
            $table->enum('temporada', ['invierno', 'primavera', 'verano', 'otono'])
                  ->nullable()->after('longitud');
            $table->enum('licencia', ['interauton', 'auton_1', 'auton_5', 'coto', 'mar'])
                  ->nullable()->after('temporada');
        });
    }

    public function down(): void
    {
        Schema::table('publicaciones', function (Blueprint $table) {
            $table->dropColumn(['temporada', 'licencia']);
        });
    }
};
