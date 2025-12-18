<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Departamento;
use App\Models\Responsavel;

class DashboardController extends Controller
{
    public function index(){
        $patrimonioTotal = $this->pegarTotalPatrimonio();
        $responsavelTotal = Responsavel::count();
        $movimentacaoTotal = $this->pegarTotalMovimentacao();
        $categoriaTotal = $this->pegarTotalCategoria();
        $localTotal = $this->PegarTotalLocal();
        $departamentoTotal = Departamento::count();
        $patrimonioRecentes = $this->pegarPatrimoniosRecentes();

        return Inertia::render('Dashboard',[
            'patrimonioRecentes' => $patrimonioRecentes,
            'departamentoTotal' =>$departamentoTotal,
            'localTotal' => $localTotal,
            'categoriaTotal' =>$categoriaTotal,
            'responsavelTotal' =>$responsavelTotal,
            'patrimonioTotal' => $patrimonioTotal
        ]);
    }

    public function pegarPatrimoniosRecentes(){

    }

    public function pegarTotalDepartamento(){

    }

    public function PegarTotalLocal(){

    }

    public function pegarTotalCategoria(){

    }

    public function pegarTotalMovimentacao(){

    }

    public function pegarTotalResponsavel(){

    }

    public function pegarTotalPatrimonio(){

    }
}
