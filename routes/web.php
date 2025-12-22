<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\ResponsavelController;
use App\Http\Controllers\ConfiguracaoController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\PatrimonioController;
use App\Http\Controllers\LocalController;
use App\Http\Controllers\MovimentacaoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UtilizadorController;
use App\Http\Controllers\CargoController;
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

    //Rotas configuracao
    Route::get('/configuracoes', [ConfiguracaoController::class, 'index'])->name('configuracao') ;
    Route::post('/configuracao/actualizar-perfil', [ConfiguracaoController::class, 'actualizar_perfil'])->name('configuracao.update.perfil');
    Route::post('/configuracao/actualizar-definicao', [ConfiguracaoController::class, 'actualizar_definicao'])->name('configuracao.update.definicao');

    //Rotas Utilizadores
    Route::get('/utilizadores', [UtilizadorController::class, 'index'])->name('users') ;
    Route::get('/utilizadores/dados', [UtilizadorController::class, 'dados_utilizadores'])->name('utilizadores.dados');
    Route::post('/utilizador/registar', [UtilizadorController::class, 'registar_utilizador'])->name('utilizador.registar');
    Route::post('/utilizadores/eliminar', [UtilizadorController::class, 'eliminar_utilizador'])->name('utilizador.eliminar');
    Route::post('/utilizadores/editar', [UtilizadorController::class, 'editar_utilizador'])->name('utilizador.editar');

    //Rotas departamento
    Route::get('/departamentos', [DepartamentoController::class, 'index'])->name('departamento') ;
    Route::post('/departamento/registar', [DepartamentoController::class, 'registar_departamento'])->name('departamento.registar');
    Route::get('/departamento/dados', [DepartamentoController::class, 'dados_departamento'])->name('departamento.dados');
    Route::post('/departamento/eliminar', [DepartamentoController::class, 'eliminar_departamento'])->name('departamento.eliminar');
    Route::post('/departamento/editar', [DepartamentoController::class, 'editar_departamento'])->name('departamento.editar');

    //Rotas cargo
    Route::get('/cargos', [CargoController::class, 'index'])->name('cargo') ;
    Route::post('/cargo/registar', [CargoController::class, 'registar_cargo'])->name('cargo.registar');
    Route::get('/cargo/dados', [CargoController::class, 'dados_cargo'])->name('cargo.dados');
    Route::post('/cargo/eliminar', [CargoController::class, 'eliminar_cargo'])->name('cargo.eliminar');
    Route::post('/cargo/editar', [CargoController::class, 'editar_cargo'])->name('cargo.editar');

    //Rotas responsaveis
    Route::get('/responsaveis', [ResponsavelController::class, 'index'])->name('responsavel') ;
    Route::post('/responsavel/registar', [ResponsavelController::class, 'registar_responsavel'])->name('responsavel.registar');
    Route::get('/responsavel/dados', [ResponsavelController::class, 'dados_responsaveis'])->name('responsavel.dados');
    Route::post('/responsavel/eliminar', [ResponsavelController::class, 'eliminar_responsavel'])->name('responsavel.eliminar');
    Route::post('/responsavel/editar', [ResponsavelController::class, 'editar_responsavel'])->name('responsavel.editar');

    //Rotas locais
    Route::get('/localizacoes', [LocalController::class, 'index'])->name('local') ;

    //Rotas patrimonio
    Route::get('/patrimonios', [PatrimonioController::class, 'index'])->name('patrimonio') ;
    Route::get('/registar-patrimonio', [PatrimonioController::class, 'index_registar'])->name('registar.patrimonio') ;

    //Rotas movimentacoes
    Route::get('/movimentacoes',[MovimentacaoController::class, 'index'])->name('movimentacao') ;

    //Rotas empresa
    Route::get('/empresa', [EmpresaController::class, 'index'])->name('empresa') ;
    Route::post('/empresa/actualizar', [EmpresaController::class, 'index'])->name('empresa.update');


    //Rotas categoria
    Route::get('/categorias', [CategoriaController::class, 'index'])->name('categoria') ;


     //Rotas relatorios
    Route::get('/relatorios',[RelatorioController::class, 'index'])->name('relatorio') ;

});

require __DIR__.'/auth.php';
