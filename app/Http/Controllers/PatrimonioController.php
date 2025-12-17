<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PatrimonioController extends Controller
{
    public function index(){
        return Inertia::render('Patrimonio/Patrimonio',[
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
                ]
            ]);
    }

}
