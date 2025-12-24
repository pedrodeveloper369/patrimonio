<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Departamento;
use App\Models\Responsavel;

class DashboardController extends Controller
{
    public function index(){
        return Inertia::render('Dashboard',[
            'patrimonioRecentes' => $this->pegarPatrimoniosRecentes(),
            'departamentoTotal' => Departamento::count(),
            'localTotal' => $this->PegarTotalLocal(),
            'categoriaTotal' => $this->pegarTotalCategoria(),
            'responsavelTotal' => Responsavel::count(),
            'patrimonioTotal' => $this->pegarTotalPatrimonio(),
            'movimentacaoTotal' => $this->pegarTotalMovimentacao(),
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
