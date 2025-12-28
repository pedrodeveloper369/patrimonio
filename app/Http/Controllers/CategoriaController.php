<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\Helper;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    protected Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

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


    public function registar_categoria(Request $request){
        $validacao = $this->validarcategoria($request);

        try {
            DB::beginTransaction();
            $categoria = new Categoria();
            $categoria->nome = $this->helper->formatarNomeProprio($request->nome);
            $categoria->save();
            DB::commit();
            return redirect()->route('categoria')
                     ->with('success', 'categoria registado com sucesso!');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('categoria')
                    ->with('erro', 'Ocorreu um erro ao registar o categoria \n'.$th->getMessage());
        }
    }

    public function eliminar_categoria(Request $request){
         try {
            // Eliminar os utilizadores dentro de uma transação
            $this->eliminarcategoria($request->ids);
            return redirect()->route('categoria')
                     ->with('success', 'Eliminação bem sucedida!');
        } catch (\Exception $e) {
           return redirect()->route('categoria')
                    ->with('erro', 'Ocorreu um erro ao eliminar \n'.$th->getMessage());
        }
    }

    public function editar_categoria(Request $request){
        $validacao = $this->validarcategoria($request);

         try {
            $categoria = Categoria::findOrFail($request->id);

            DB::beginTransaction();
            $categoria->nome = $this->helper->formatarNomeProprio($request->nome);
            $categoria->save();
            DB::commit();
            return redirect()->route('categoria')
                     ->with('success', 'categoria registado com sucesso!');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('categoria')
                    ->with('erro', 'Ocorreu um erro ao registar o categoria \n'.$th->getMessage());
        }
    }

    //elimina os categoria
    public function eliminarcategoria($ids){
        DB::transaction(function () use ($ids) {
            Categoria::whereIn('id', $ids)->delete();
        });
    }

    //funcao que pega da base de dados todos os categorias
    public function dados_categoria(){
        return Categoria::all();
    }

    //Funcao que valida os dados do formulario do utilizador
    private function validarcategoria($request)
    {
        return $request->validate(
            [
                'nome' => ['required', 'min:3', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÂÊÔâêôÃÕãõçÇ\s]+$/','unique:categorias,nome'],
            ],
            [
                'nome.required' => 'O nome é obrigatório.',
                'nome.min' => 'O nome deve ter pelo menos 3 caracteres.',
                'nome.regex' => 'O nome deve conter apenas letras e espaços.',
                'nome.unique' => 'Este categoria já está registado.',
            ]
        );
    }

}
