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
        Schema::create('patrimonio_campos_dinamicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_patrimonio')->constrained('patrimonios')->onDelete('cascade');
            $table->foreignId('id_categoria_campo')->constrained('categoria_campos_dinamicos')->onDelete('cascade');
            $table->text('valor'); // O valor preenchido pelo usuário
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patrimonio_campos_dinamicos');
    }
};
