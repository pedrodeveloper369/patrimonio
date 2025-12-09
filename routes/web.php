<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnidadeController;
use App\Http\Controllers\ResponsavelController;
use App\Http\Controllers\ConfiguracaoController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\PatrimonioController;
use App\Http\Controllers\LocalController;
use App\Http\Controllers\MovimentacaoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UtilizadorController;
use App\Http\Controllers\EmpresaController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if(!Auth::check() == true) {
        return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
    }
    return redirect()->route('dashboard');
});


Route::middleware(['auth', 'verified'])->group(function () {
     //Rotas Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard') ;

    //Rotas Utilizadores
    Route::get('/utilizadores', [UtilizadorController::class, 'index'])->name('users') ;
    Route::get('/utilizadores/dados', [UtilizadorController::class, 'dados_utilizadores'])->name('utilizadores.dados');
    Route::post('/utilizador/registar', [UtilizadorController::class, 'registar_utilizador'])->name('utilizador.registar');
    Route::post('/utilizadores/eliminar', [UtilizadorController::class, 'eliminar_utilizador'])->name('utilizador.eliminar');
    Route::post('/utilizadores/editar', [UtilizadorController::class, 'editar_utilizador'])->name('utilizador.editar');


    //Rotas patrimonio
    Route::get('/patrimonio', [PatrimonioController::class, 'index'])->name('patrimonio') ;

    //Rotas unidades
    Route::get('/unidades', [UnidadeController::class, 'index'])->name('unidade') ;

    //Rotas responsaveis
    Route::get('/responsaveis', [ResponsavelController::class, 'index'])->name('responsavel') ;


    //Rotas movimentacoes
    Route::get('/movimentacoes',[MovimentacaoController::class, 'index'])->name('movimentacao') ;

    //Rotas locais
    Route::get('/locais', [LocalController::class, 'index'])->name('local') ;

    //Rotas configuracao
    Route::get('/configuracao', [ConfiguracaoController::class, 'index'])->name('configuracao') ;
    Route::post('/configuracao/actualizar-perfil', [ConfiguracaoController::class, 'actualizar_perfil'])->name('configuracao.update.perfil');
    Route::post('/configuracao/actualizar-definicao', [ConfiguracaoController::class, 'actualizar_definicao'])->name('configuracao.update.definicao');

    //Rotas empresa
    Route::get('/empresa', [EmpresaController::class, 'index'])->name('empresa') ;
    Route::post('/empresa/actualizar', [EmpresaController::class, 'index'])->name('empresa.update');


    //Rotas categoria
    Route::get('/categorias', [CategoriaController::class, 'index'])->name('categoria') ;


     //Rotas relatorios
    Route::get('/relatorios',[RelatorioController::class, 'index'])->name('relatorio') ;











    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
