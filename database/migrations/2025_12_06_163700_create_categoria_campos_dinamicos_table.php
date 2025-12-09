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
        Schema::create('categoria_campos_dinamicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_categoria')->constrained('categorias')->onDelete('cascade');
            $table->string('nome'); // Ex: "número de série", "placa", "cor"
            $table->string('tipo'); // Ex: text, number, date, select, boolean
            $table->boolean('obrigatorio')->default(false);
            $table->integer('ordem')->nullable();
            // Caso seja um SELECT, RADIO ou CHECKBOX, colocamos as opções em JSON
            $table->json('opcoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoria_campos_dinamicos');
    }
};
