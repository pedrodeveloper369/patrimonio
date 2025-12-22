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
        EstadoPatrimonio::create([ 'nome' => 'Activo', 'cor' => '#22C55E']);
        EstadoPatrimonio::create([ 'nome' => 'Inactivo', 'cor' => '#6B7280']);
        EstadoPatrimonio::create([ 'nome' => 'Disponível', 'cor' => '#3B82F6']);
        EstadoPatrimonio::create([ 'nome' => 'Danificado', 'cor' => '#F97316']);
        EstadoPatrimonio::create([ 'nome' => 'Em manutenção', 'cor' => '#EAB308']);
        EstadoPatrimonio::create([ 'nome' => 'Perdido', 'cor' => '#EF4444']);
        EstadoPatrimonio::create([ 'nome' => 'Obsoleto', 'cor' => '#4B5563']);
        EstadoPatrimonio::create([ 'nome' => 'Transferido', 'cor' => '#1D4ED8']);
        EstadoPatrimonio::create([ 'nome' => 'Emprestado', 'cor' => '#8B5CF6']);
        EstadoPatrimonio::create([ 'nome' => 'Descartado', 'cor' => '#111827']);
    }
}
