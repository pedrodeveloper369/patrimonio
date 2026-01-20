<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\Helper;
use App\Models\EstadoPatrimonio;
use App\Models\Departamento;
use App\Models\TipoLocal;
use App\Models\Responsavel;
use App\Models\Categoria;
use App\Models\Patrimonio;
use App\Models\Local;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PatrimonioController extends Controller
{
    protected Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    public function index(){
        return Inertia::render('Patrimonio/Patrimonio',[
            'estado_patrimonio' => EstadoPatrimonio::all(),
            'departamento' => Departamento::all(),
            'categoria' => Categoria::all(),
            'localizacao' => TipoLocal::all(),
            'responsavel' => Responsavel::all(),
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
                ]
            ]);
    }

    public function index_registar(){
        return Inertia::render('Patrimonio/Registar_Patrimonio',[
            'caminhosLocal' => $this->caminhos_organizados(),
            'responsaveis' => $this->buscaResponsaveis(),
            'categorias' => Categoria::all(),
            'estadoPatrimonio' => EstadoPatrimonio::all(),
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
                ]
            ]);
    }

    public function index_editar($id_patrimonio){

        return Inertia::render('Patrimonio/Editar_Patrimonio',[
            'caminhosLocal' => $this->caminhos_organizados(),
            'responsaveis' => $this->buscaResponsaveis(),
            'categorias' => Categoria::all(),
            'estadoPatrimonio' => EstadoPatrimonio::all(),
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
                ]
            ]);
    }

    //Rota, funcao de registo de patrimonio
    public function registar_patrimonio(Request $request){
        $validacao = $this->validarPatrimonio($request);
        try {

            DB::beginTransaction();
            $patrimonio = new Patrimonio();
            $patrimonio->nome = $this->helper->formatarNomeProprio($request->nome);
            $patrimonio->codigo = $request->codigo;
            $patrimonio->descricao = $request->descricao;
            $patrimonio->qtd = $request->qtd;
            $patrimonio->imagem = $request->imagem;
            $patrimonio->valor_compra = $request->valor_compra;
            $patrimonio->origem = $request->origem;
            $patrimonio->conservacao = $request->conservacao;
            $patrimonio->documento = $request->documento;
            $patrimonio->id_categoria = $request->id_categoria;
            $patrimonio->id_localizacao = $request->id_local;
            $patrimonio->id_estado_patrimonio = $request->id_estado_patrimonio;
            $patrimonio->marca = $request->marca;
            $patrimonio->cor = $request->cor;
            $patrimonio->num_serie = $request->num_serie;
            $patrimonio->id_responsavel = $request->responsavel;
            $patrimonio->save();

            DB::commit();
            return redirect()->route('registar.patrimonio')
                     ->with('success', 'Património registado com sucesso!');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('registar.patrimonio')
                    ->with('erro', 'Ocorreu um erro ao registar o patrimoónio \n'.$th->getMessage());
        }
    }

     //funcao que pega da base de dados todos os patrimonios
    public function dados_patrimonios(){
        $query = DB::table('patrimonios as Patr')
            ->select(
                'Patr.id',
                'Patr.nome',
                'Patr.codigo',
                'Patr.descricao',
                'Patr.qtd',
                'Patr.imagem',
                'Patr.valor_compra',
                'Patr.origem',
                'Patr.conservacao',
                'Patr.documento',
                'Patr.marca',
                'Patr.num_serie',
                'Cat.nome as categoria',
                'Est.nome as estado_patrimonio',
                'Est.cor',
                'Resp.nome as responsavel',
                'Lo.nome as localizacao'
            )
            ->leftJoin('categorias as Cat', 'Cat.id', '=', 'Patr.id_categoria')
            ->leftJoin('estado_patrimonios as Est', 'Est.id', '=', 'Patr.id_estado_patrimonio')
            ->leftJoin('responsavels as Resp', 'Resp.id', '=', 'Patr.id_responsavel')
            ->leftJoin('locals as Lo', 'Lo.id', '=', 'Patr.id_localizacao');

        return DataTables::of($query)->make(true);
    }

    //funcao que pega da base de dados todos os responsaveles
    public function buscaResponsaveis(){
        return DB::table('responsavels as Resp')
            ->select(
                'Resp.id',
                'Resp.nome',
                'Dep.nome as departamento',
            )
            ->leftJoin('departamentos as Dep', 'Dep.id', '=', 'Resp.id_departamento')
            ->get();
    }

    //localizacao
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

     //Funcao que valida os dados do formulario do patrimonio
    private function validarPatrimonio($request)
    {
        return $request->validate(
            [
                'nome' => ['required', 'min:3', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÂÊÔâêôÃÕãõçÇ\s]+$/'],
                'codigo' => ['required'],
                'conservacao' => ['required'],
                'id_categoria' => ['required'],
                'id_estado_patrimonio' => ['required'],
                'descricao' => ['nullable','min:3'],

            ],
            [
                'nome.required' => 'O nome é obrigatório.',
                'nome.min' => 'O nome deve ter pelo menos 3 caracteres.',
                'nome.regex' => 'O nome deve conter apenas letras e espaços.',
                'codigo.required' => 'O codigo é obrigatório.',
                'conservacao.required' => 'O conservacao é obrigatório.',
                'id_categoria.required' => 'A categoria é obrigatório.',
                'id_estado_patrimonio.required' => 'O estado é obrigatório.',
                'descricao.min' => 'A descriçãó deve ter pelo menos 3 caracteres.',
            ]
        );
    }


}
