<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Cargo;
use Illuminate\Support\Facades\DB;
use App\Services\Helper;

class CargoController extends Controller
{
    protected Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    public function index(){
        return Inertia::render('Cargo/Cargo',[
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
                ]
            ]);
    }

     public function registar_cargo(Request $request){
        $validacao = $this->validarcargo($request);

        try {
            DB::beginTransaction();
            $cargo = new Cargo();
            $cargo->nome = $this->helper->formatarNomeProprio($request->nome);
            $cargo->save();
            DB::commit();
            return redirect()->route('cargo')
                     ->with('success', 'cargo registado com sucesso!');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('cargo')
                    ->with('erro', 'Ocorreu um erro ao registar o cargo \n'.$th->getMessage());
        }
    }

     public function eliminar_cargo(Request $request){
         try {
            // Eliminar os utilizadores dentro de uma transação
            $this->eliminarcargo($request->ids);
            return redirect()->route('cargo')
                     ->with('success', 'Eliminação bem sucedida!');
        } catch (\Exception $e) {
           return redirect()->route('cargo')
                    ->with('erro', 'Ocorreu um erro ao eliminar \n'.$th->getMessage());
        }
    }

    public function editar_cargo(Request $request){
        $validacao = $this->validarcargo($request);

         try {
            $cargo = Cargo::findOrFail($request->id);

            DB::beginTransaction();
            $cargo->nome = $this->helper->formatarNomeProprio($request->nome);
            $cargo->save();
            DB::commit();
            return redirect()->route('cargo')
                     ->with('success', 'cargo registado com sucesso!');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('cargo')
                    ->with('erro', 'Ocorreu um erro ao registar o cargo \n'.$th->getMessage());
        }
    }

    //elimina os cargo
    public function eliminarcargo($ids){
        DB::transaction(function () use ($ids) {
            Cargo::whereIn('id', $ids)->delete();
        });
    }

    //funcao que pega da base de dados todos os cargos
    public function dados_cargo(){
        return Cargo::all();
    }

    //Funcao que valida os dados do formulario do utilizador
    private function validarcargo($request)
    {
        return $request->validate(
            [
                'nome' => ['required', 'min:3', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÂÊÔâêôÃÕãõçÇ\s]+$/','unique:cargos,nome'],
            ],
            [
                'nome.required' => 'O nome é obrigatório.',
                'nome.min' => 'O nome deve ter pelo menos 3 caracteres.',
                'nome.regex' => 'O nome deve conter apenas letras e espaços.',
                'nome.unique' => 'Este cargo já está registado.',
            ]
        );
    }

}
