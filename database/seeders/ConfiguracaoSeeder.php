<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EstadoPatrimonio;
use App\Models\TipoLocal;

class ConfiguracaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //configuracoes iniciais do estado que os patrimonios vai assumir
        EstadoPatrimonio::create([ 'nome' => 'Activo', 'cor' => '#15803D', 'background' => '#DCFCE7']);
        EstadoPatrimonio::create([ 'nome' => 'Inactivo', 'cor' => '#374151' , 'background' => '#F3F4F6']);
        EstadoPatrimonio::create([ 'nome' => 'Disponível', 'cor' => '#1D4ED8' , 'background' => '#DBEAFE']);
        EstadoPatrimonio::create([ 'nome' => 'Danificado', 'cor' => '#C2410C' , 'background' => '#FFEDD5']);
        EstadoPatrimonio::create([ 'nome' => 'Em manutenção', 'cor' => '#A16207' , 'background' => '#FEF9C3']);
        EstadoPatrimonio::create([ 'nome' => 'Perdido', 'cor' => '#B91C1C' , 'background' => '#FEE2E2']);
        EstadoPatrimonio::create([ 'nome' => 'Obsoleto', 'cor' => '#374151' , 'background' => '#E5E7EB']);
        EstadoPatrimonio::create([ 'nome' => 'Transferido', 'cor' => '#1D4ED8' , 'background' => '#E0E7FF']);
        EstadoPatrimonio::create([ 'nome' => 'Emprestado', 'cor' => '#6D28D9' , 'background' => '#EDE9FE']);
        EstadoPatrimonio::create([ 'nome' => 'Descartado', 'cor' => '#111827' , 'background' => '#F9FAFB']);

        //configuração inicial dos tipos de localização
        TipoLocal::create([ 'nome' => 'Pátio']);
        TipoLocal::create([ 'nome' => 'Bloco']);
        TipoLocal::create([ 'nome' => 'Edifício']);
        TipoLocal::create([ 'nome' => 'Andar']);
        TipoLocal::create([ 'nome' => 'Corredor']);
        TipoLocal::create([ 'nome' => 'Hall']);
        TipoLocal::create([ 'nome' => 'Sala']);
        TipoLocal::create([ 'nome' => 'Sanitária']);
        TipoLocal::create([ 'nome' => 'Armazem']);
        TipoLocal::create([ 'nome' => 'Estante']);
    }
}
