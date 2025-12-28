<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\Helper;
use App\Models\EstadoPatrimonio;
use App\Models\Departamento;
use App\Models\TipoLocal;
use App\Models\Responsavel;
use App\Models\Categoria;

class PatrimonioController extends Controller
{
    protected Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    public function index(){
        return Inertia::render('Patrimonio/Patrimonio',[
            'estado_patrimonio' => EstadoPatrimonio::all(),
            'departamento' => Departamento::all(),
            'categoria' => Categoria::all(),
            'localizacao' => TipoLocal::all(),
            'responsavel' => Responsavel::all(),
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
                ]
            ]);
    }

    public function index_registar(){
        return Inertia::render('Patrimonio/Registar_Patrimonio',[
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
                ]
            ]);
    }

    public function index_editar($id_patrimonio){
     
        return Inertia::render('Patrimonio/Editar_Patrimonio',[
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
                ]
            ]);
    }






}
