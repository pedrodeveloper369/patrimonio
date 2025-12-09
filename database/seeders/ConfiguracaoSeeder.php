<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EstadoPatrimonio;

class ConfiguracaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EstadoPatrimonio::create([ 'nome' => 'Activo']);
        EstadoPatrimonio::create([ 'nome' => 'Inactivo']);
        EstadoPatrimonio::create([ 'nome' => 'Disponível']);
        EstadoPatrimonio::create([ 'nome' => 'Danificado']);
        EstadoPatrimonio::create([ 'nome' => 'Em manutenção']);
        EstadoPatrimonio::create([ 'nome' => 'Perdido']);
        EstadoPatrimonio::create([ 'nome' => 'Obsoleto']);
        EstadoPatrimonio::create([ 'nome' => 'Transferido']);
        EstadoPatrimonio::create([ 'nome' => 'Emprestado']);
        EstadoPatrimonio::create([ 'nome' => 'Descartado']);
    }
}
