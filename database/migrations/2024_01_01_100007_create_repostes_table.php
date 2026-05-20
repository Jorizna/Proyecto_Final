<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repostes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('publicacion_id')->constrained('publicaciones')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'publicacion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repostes');
    }
};
