<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

class RelatorioController extends Controller
{
     public function index(){
        return Inertia::render('Relatorio/Relatorio');
    }

    public function relatorio(Request $request)
    {
        $dados = Patrimonio::when($request->estado, function ($q) use ($request) {
            $q->where('estado', $request->estado);
        })->get();

        return Pdf::loadView('Relatorio.Relatorio', compact('dados'))->setPaper('a4', 'portrait')
            ->download('relatorio.pdf');
    }


}
