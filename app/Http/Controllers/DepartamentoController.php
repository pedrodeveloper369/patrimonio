<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Departamento;
use Illuminate\Support\Facades\DB;
use App\Services\Helper;

class DepartamentoController extends Controller
{
    protected Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    public function index(){
        $departamento = Departamento::all();
        return Inertia::render('Departamento/Departamento',[
            'departamento' => $departamento,
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
                ]
            ]);
    }

    public function registar_departamento(Request $request){
        $validacao = $this->validarDepartamento($request);

        try {
            DB::beginTransaction();
            $departamento = new Departamento();
            $departamento->nome = $this->helper->formatarNomeProprio($request->nome);
            $departamento->sigla = $request->sigla;
            $departamento->save();
            DB::commit();
            return redirect()->route('departamento')
                     ->with('success', 'Departamento registado com sucesso!');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('departamento')
                    ->with('erro', 'Ocorreu um erro ao registar o departamento \n'.$th->getMessage());
        }
    }

    public function eliminar_departamento(Request $request){
         try {
            // Eliminar os utilizadores dentro de uma transação
            $this->eliminarDepartamento($request->ids);
            return redirect()->route('departamento')
                     ->with('success', 'Eliminação bem sucedida!');
        } catch (\Exception $e) {
           return redirect()->route('departamento')
                    ->with('erro', 'Ocorreu um erro ao eliminar \n'.$th->getMessage());
        }
    }

    public function editar_departamento(Request $request){
        $validacao = $this->validarDepartamento($request);

         try {
            $departamento = Departamento::findOrFail($request->id);

            DB::beginTransaction();
            $departamento->nome = $this->helper->formatarNomeProprio($request->nome);
            $departamento->sigla = $request->sigla;
            $departamento->save();
            DB::commit();
            return redirect()->route('departamento')
                     ->with('success', 'Departamento registado com sucesso!');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('departamento')
                    ->with('erro', 'Ocorreu um erro ao registar o departamento \n'.$th->getMessage());
        }
    }


    //elimina os departamento
    public function eliminarDepartamento($ids){
        DB::transaction(function () use ($ids) {
            Departamento::whereIn('id', $ids)->delete();
        });
    }

    //funcao que pega da base de dados todos os departamentos
    public function dados_departamento(){
        return Departamento::all();
    }


    //Funcao que valida os dados do formulario do utilizador
    private function validarDepartamento($request)
    {
        return $request->validate(
            [
                'nome' => ['required', 'min:3', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÂÊÔâêôÃÕãõçÇ\s]+$/','unique:departamentos,nome'],
            ],
            [
                'nome.required' => 'O nome é obrigatório.',
                'nome.min' => 'O nome deve ter pelo menos 3 caracteres.',
                'nome.regex' => 'O nome deve conter apenas letras e espaços.',
                'nome.unique' => 'Este departamento já está registado.',
            ]
        );
    }

}
