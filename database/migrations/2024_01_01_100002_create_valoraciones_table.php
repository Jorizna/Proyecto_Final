<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('valoraciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publicacion_id')->constrained('publicaciones')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('puntuacion');
            $table->timestamps();

            $table->unique(['publicacion_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('valoraciones');
    }
};
