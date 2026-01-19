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

class DashboardController extends Controller
{
    public function index(){
        return Inertia::render('Dashboard',[
            'patrimonioRecentes' => Patrimonio::latest()->take(6)->get(),
            'departamentoTotal' => Departamento::count(),
            'localTotal' => Local::count(),
            'categoriaTotal' => Categoria::count(),
            'responsavelTotal' => Responsavel::count(),
            'patrimonioTotal' => Patrimonio::count(),
            'movimentacaoTotal' => Movimentacao::count(),
        ]);
    }
}
