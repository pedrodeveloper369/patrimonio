<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Services\Helper;
use App\Models\Local;

class LocalController extends Controller
{
    protected Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    public function index(){
        return Inertia::render('Local/Local',[
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
                ]
            ]);
    }

    public function registar_local(Request $request){
        $validacao = $this->validarlocal($request);

        try {
            DB::beginTransaction();
            $local = new Local();
            $local->nome = $this->helper->formatarNomeProprio($request->nome);
            $local->save();
            DB::commit();
            return redirect()->route('local')
                     ->with('success', 'local registado com sucesso!');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('local')
                    ->with('erro', 'Ocorreu um erro ao registar o local \n'.$th->getMessage());
        }
    }

     public function eliminar_local(Request $request){
         try {
            // Eliminar os utilizadores dentro de uma transação
            $this->eliminarlocal($request->ids);
            return redirect()->route('local')
                     ->with('success', 'Eliminação bem sucedida!');
        } catch (\Exception $e) {
           return redirect()->route('local')
                    ->with('erro', 'Ocorreu um erro ao eliminar \n'.$th->getMessage());
        }
    }

    public function editar_local(Request $request){
        $validacao = $this->validarlocal($request);

         try {
            $local = Local::findOrFail($request->id);

            DB::beginTransaction();
            $local->nome = $this->helper->formatarNomeProprio($request->nome);
            $local->save();
            DB::commit();
            return redirect()->route('local')
                     ->with('success', 'local registado com sucesso!');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('local')
                    ->with('erro', 'Ocorreu um erro ao registar o local \n'.$th->getMessage());
        }
    }

    //elimina os local
    public function eliminarlocal($ids){
        DB::transaction(function () use ($ids) {
            Local::whereIn('id', $ids)->delete();
        });
    }

    //funcao que pega da base de dados todos os locals
    public function dados_local(){
        return Local::all();
    }

    //Funcao que valida os dados do formulario do utilizador
    private function validarlocal($request)
    {
        return $request->validate(
            [
                'nome' => ['required', 'min:3', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÂÊÔâêôÃÕãõçÇ\s]+$/','unique:locals,nome'],
            ],
            [
                'nome.required' => 'O nome é obrigatório.',
                'nome.min' => 'O nome deve ter pelo menos 3 caracteres.',
                'nome.regex' => 'O nome deve conter apenas letras e espaços.',
            ]
        );
    }
}
