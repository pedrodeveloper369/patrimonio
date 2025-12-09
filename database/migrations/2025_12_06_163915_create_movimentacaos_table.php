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
        Schema::create('movimentacaos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_patrimonio')->constrained('patrimonios')->onDelete('cascade');
            $table->foreignId('id_unidade')->constrained('unidades')->onDelete('cascade');
            $table->foreignId('origem')->constrained('locals')->onDelete('cascade');
            $table->foreignId('destino')->constrained('locals')->onDelete('cascade');
            $table->foreignId('antigo_responsavel')->constrained('responsavels')->onDelete('cascade');
            $table->foreignId('novo_responsavel')->constrained('responsavels')->onDelete('cascade');
            $table->string('estado_patrimonio');
            $table->foreignId('id_utilizador')->constrained('users')->onDelete('cascade');
            $table->string('motivo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimentacaos');
    }
};
