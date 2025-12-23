<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Services\Helper;
use App\Models\Local;
use App\Models\TipoLocal;

class LocalController extends Controller
{
    protected Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    public function index(){

        $tipoLocal = TipoLocal::orderBy('nome', 'asc')->get();
        return Inertia::render('Local/Local',[
            'tipoLocal' => $tipoLocal,
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
                ]
        ]);
    }

    public function index_registar(){
        $tipoLocal = TipoLocal::orderBy('nome', 'asc')->get();
        $caminhosLocal = $this->caminhos_organizados();
        return Inertia::render('Local/RegistarLocal',['tipoLocal' => $tipoLocal, 'caminhosLocal' =>$caminhosLocal]);
    }

    public function registar_local(Request $request){

        $validacao = $this->validarlocal($request);

        try {
            DB::beginTransaction();
            $local = new Local();
            $local->nome = $this->helper->formatarNomeProprio($request->nome);
            $local->id_tipolocal = $request->tipoLocalizacao;
            $local->parent_id = $request->id_local; //este é o id do espaco geral onde o local a ser cadastrado pertencerá
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

    function caminho(Local $local)
    {
        $path = [];

        while ($local) {
            $path[] = $local->nome;
            $local = $local->parent;
        }

        return implode(' → ', array_reverse($path));
    }

    public function caminhos_organizados()
    {
        return Local::with(['children.children.children', 'tipo'])
            ->whereNull('parent_id')
            ->get()
            ->map(fn ($l) => $this->mapLocal($l));
    }

    private function mapLocal($local)
    {
        return [
            'id' => $local->id,
            'nome' => $local->nome,
            'caminho' => $this->caminho($local),
            'children' => $local->children->map(fn ($c) => $this->mapLocal($c))
        ];
    }

    //Funcao que valida os dados do formulario do utilizador
    private function validarlocal($request)
    {
        return $request->validate(
            [
                'nome' => ['required', 'min:3', 'regex:/^[0-9A-Za-zÁÉÍÓÚáéíóúÂÊÔâêôÃÕãõçÇ\s]+$/'],
                'tipoLocalizacao' => ['required'],
            ],
            [
                'nome.required' => 'O nome é obrigatório.',
                'nome.min' => 'O nome deve ter pelo menos 3 caracteres.',
                'nome.regex' => 'O nome deve conter apenas letras e espaços.',

                'tipoLocalizacao.required' => 'O nome é obrigatório.',
            ]
        );
    }
}
