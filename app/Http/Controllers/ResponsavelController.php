<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\Helper;
use App\Models\Cargo;
use App\Models\Departamento;
use App\Models\Responsavel;
use Illuminate\Support\Facades\DB;

class ResponsavelController extends Controller
{
    protected Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    public function index(){
        return Inertia::render('Responsavel/Responsavel',[
            'departamento' => Departamento::all(),
            'cargo' => Cargo::all(),
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
                ]
            ]);
    }

    //Rota, funcao de registo de responsavel
    public function registar_responsavel(Request $request){
        $validacao = $this->validarResponsavel($request);

        try {

            DB::beginTransaction();
            $responsavel = new Responsavel();
            $responsavel->nome = $this->helper->formatarNomeProprio($request->nome);
            $responsavel->contacto = $request->contacto;
            $responsavel->id_departamento = $request->departamento;
            $responsavel->id_cargo = $request->cargo;
            $responsavel->save();
            DB::commit();

            return redirect()->route('responsavel')
                     ->with('success', 'Responsavel registado com sucesso!');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('responsavel')
                    ->with('erro', 'Ocorreu um erro ao registar o responsavel \n'.$th->getMessage());
        }
    }

    //Rota. funcao eliminar responsaveles
    public function eliminar_responsavel(Request $request){
        $ids = $request->ids;
        try {
            // Eliminar os responsaveles dentro de uma transação
            DB::transaction(function () use ($ids) {
                Responsavel::whereIn('id', $ids)->delete();
         }  );
            return redirect()->route('responsavel')
                     ->with('success', 'Eliminação bem sucedida!');
        } catch (\Exception $th) {
           return redirect()->route('responsavel')
                    ->with('erro', 'Ocorreu um erro ao eliminar \n'.$th->getMessage());
        }
    }

    //Rota, funcao de editar responsavel
    public function editar_responsavel(Request $request){
        $validacao = $this->validarResponsavel($request);
        try {
            $responsavel = Responsavel::findOrFail($request->id);

            DB::beginTransaction();
            $responsavel->nome = $this->helper->formatarNomeProprio($request->nome);
            $responsavel->contacto = $request->contacto;
            $responsavel->id_departamento = $request->departamento;
            $responsavel->id_cargo = $request->cargo;
            $responsavel->save();
            DB::commit();

            return redirect()->route('responsavel')
                     ->with('success', 'Responsavel Actualizado com sucesso!');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('responsavel')
                    ->with('erro', 'Ocorreu um erro ao Actualizar o responsavel \n'.$th->getMessage());
        }
    }

    //funcao que pega da base de dados todos os responsaveles
    public function dados_responsaveis(){
        return DB::table('responsavels as Resp')
            ->select(
                'Resp.id',
                'Resp.nome',
                'Resp.contacto',
                'Resp.created_at',
                'Dep.nome as departamento',
                'C.nome as cargo',
                'Dep.id as id_departamento',
                'C.id as id_cargo',
            )
            ->leftJoin('departamentos as Dep', 'Dep.id', '=', 'Resp.id_departamento')
            ->leftJoin('cargos as C', 'C.id', '=', 'Resp.id_cargo')
            ->get();
    }

    //Funcao que valida os dados do formulario do responsavel
    private function validarResponsavel($request)
    {
        return $request->validate(
            [
                'nome' => ['required', 'min:3', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÂÊÔâêôÃÕãõçÇ\s]+$/', 'unique:responsavels,nome'],
                'contacto' => ['required', 'digits_between:9,12'],
                'departamento' => ['required'],
                'cargo' => ['required'],
            ],
            [
                'nome.required' => 'O nome é obrigatório.',
                'nome.min' => 'O nome deve ter pelo menos 3 caracteres.',
                'nome.regex' => 'O nome deve conter apenas letras e espaços.',
                'nome.unique' => 'Este responsável já está registado.',

                'contacto.required' => 'O contacto é obrigatório.',
                'contacto.digits_between' => 'O contacto deve ter entre 9 e 12 dígitos.',

                'departamento.required' => 'O departamento é obrigatório.',

                'cargo.required' => 'O cargo é obrigatório.',
            ]
        );
    }

}
