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
        Schema::create('patrimonios', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('código')->nullable();
            $table->string('descricao')->nullable();
            $table->string('qtd')->nullable();
            $table->string('imagem')->nullable();
            $table->foreignId('id_categoria')->constrained('categorias')->onDelete('cascade');
            $table->foreignId('id_localizacao')->constrained('locals')->onDelete('cascade');
            $table->foreignId('id_estado_patrimonio')->constrained('estado_patrimonios')->onDelete('cascade');
            $table->string('valor_compra')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patrimonios');
    }
};
