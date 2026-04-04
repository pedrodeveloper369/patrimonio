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
            $table->foreignId('origem')->constrained('locals')->onDelete('cascade'); //antigo local
            $table->foreignId('destino')->constrained('locals')->onDelete('cascade'); //novo local
            $table->foreignId('antigo_responsavel')->constrained('responsavels')->onDelete('cascade');
            $table->foreignId('novo_responsavel')->constrained('responsavels')->onDelete('cascade');
            $table->foreignId('id_estado_antigo')->constrained('estado_patrimonios')->onDelete('cascade');
            $table->foreignId('id_estado_novo')->constrained('estado_patrimonios')->onDelete('cascade');
            $table->foreignId('id_utilizador')->constrained('users')->onDelete('cascade');
            $table->string('motivo')->nullable();
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
