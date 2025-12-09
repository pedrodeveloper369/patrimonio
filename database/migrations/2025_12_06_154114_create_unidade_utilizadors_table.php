<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('unidade_utilizadors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_unidade')->constrained('unidades')->onDelete('cascade');
            $table->foreignId('id_utilizador')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidade_utilizadors');
    }
};
