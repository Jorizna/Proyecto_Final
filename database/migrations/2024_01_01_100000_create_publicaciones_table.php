<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descripcion');
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            $table->decimal('valoracion_media', 3, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publicaciones');
    }
};
