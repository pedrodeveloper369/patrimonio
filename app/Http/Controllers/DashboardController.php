<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Departamento;
use App\Models\Responsavel;
use App\Models\Categoria;
use App\Models\Local;
use App\Models\Patrimonio;
use App\Models\Movimentacao;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(){

        return Inertia::render('Dashboard',[
            'patrimonioRecentes' => $this->patrimonios_recentes(),
            'departamentoTotal' => Departamento::count(),
            'localTotal' => Local::count(),
            'categoriaTotal' => Categoria::count(),
            'responsavelTotal' => Responsavel::count(),
            'patrimonioTotal' => Patrimonio::where('estado','activo')->count(),
            'movimentacaoTotal' => Movimentacao::count(),
        ]);
    }

     //funcao que pega da base de dados todos os patrimonios
    public function patrimonios_recentes(){
        return DB::table('patrimonios as Patr')
            ->select(
                'Patr.id',
                'Patr.nome',
                'Patr.codigo',
                'Patr.descricao',
                'Patr.qtd',
                'Patr.imagem',
                'Patr.valor_compra',
                'Patr.origem',
                'Patr.conservacao',
                'Patr.documento',
                'Patr.marca',
                'Patr.num_serie',
                'Cat.nome as categoria',
                'Est.nome as estado_patrimonio',
                'Est.cor',
                'Est.background',
                'Resp.nome as responsavel',
                'Lo.nome as localizacao'
            )
            ->leftJoin('categorias as Cat', 'Cat.id', '=', 'Patr.id_categoria')
            ->leftJoin('estado_patrimonios as Est', 'Est.id', '=', 'Patr.id_estado_patrimonio')
            ->leftJoin('responsavels as Resp', 'Resp.id', '=', 'Patr.id_responsavel')
            ->leftJoin('locals as Lo', 'Lo.id', '=', 'Patr.id_localizacao')
            ->where('Patr.estado','=', 'activo')
            ->orderBy('Patr.id', 'desc')
            ->limit(6)
            ->get();
    }
}
