<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('valoraciones');
        Schema::dropIfExists('password_reset_tokens');

        if (Schema::hasColumn('publicaciones', 'valoracion_media')) {
            Schema::table('publicaciones', function (Blueprint $table) {
                $table->dropColumn('valoracion_media');
            });
        }
    }

    public function down(): void
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('valoraciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('publicacion_id')->constrained('publicaciones')->cascadeOnDelete();
            $table->unsignedTinyInteger('nota');
            $table->timestamps();
            $table->unique(['user_id', 'publicacion_id']);
        });

        Schema::table('publicaciones', function (Blueprint $table) {
            $table->float('valoracion_media')->default(0)->after('longitud');
        });
    }
};
