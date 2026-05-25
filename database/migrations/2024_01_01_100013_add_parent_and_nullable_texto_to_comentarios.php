<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comentarios', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('user_id')
                  ->constrained('comentarios')->onDelete('cascade');
            $table->text('texto')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('comentarios', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
            $table->text('texto')->nullable(false)->change();
        });
    }
};
