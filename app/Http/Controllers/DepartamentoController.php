<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartamentoController extends Controller
{
    public function index(){
        return Inertia::render('Departamento/Departamento',[
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
                ]
            ]);
    }
}
