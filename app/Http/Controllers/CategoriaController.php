<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoriaController extends Controller
{
     public function index(){
        return Inertia::render('Categoria/Categoria',[
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
                ]
            ]);
    }


    /*

    $request->validate([
    'nome' => 'required|string',
    'campos' => 'required|array',
    'campos.*.nome' => 'required|string',
    'campos.*.tipo' => 'required|string',
]);


*/
}
