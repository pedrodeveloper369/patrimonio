<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class LocalController extends Controller
{
    public function index(){
        return Inertia::render('Local/Local',[
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
                ]
            ]);
    }

    public function buscarLocais(){
        return Inertia::render('Local/Local',[
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
                ]
            ]);
    }
}
