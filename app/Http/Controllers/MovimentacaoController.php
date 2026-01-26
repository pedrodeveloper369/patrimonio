<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\EstadoPatrimonio;
use App\Models\Responsavel;
use App\Models\Local;
use Illuminate\Support\Facades\DB;
use App\Models\Patrimonio;
use App\Models\Movimentacao;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class MovimentacaoController extends Controller
{
    public function index(){
        //dd($this->dados_movimentacoes_patr());
        return Inertia::render('Movimentacao/Movimentacao',[
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
                ]
            ]);
    }

    public function movimentar_patrimonio($patrimonio){
        return Inertia::render('Movimentacao/Movimentar_Patrimonio',[
            'caminhosLocal' => $this->caminhos_organizados(),
            'responsaveis' => $this->buscaResponsaveis(),
            'estadoPatrimonio' => EstadoPatrimonio::all(),
            'patrimonio' => $this->dados_patrimonio($patrimonio),
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
                ]
            ]);
    }

    public function movimentarPatrimonio(Request $request){

         try {
            DB::beginTransaction();

            $patrimonio = Patrimonio::findOrFail($request->id);
            if($request->id_local){
                $patrimonio->id_localizacao = $request->id_local;
            }
            if($request->id_estado_patrimonio){
                 $patrimonio->id_estado_patrimonio = $request->id_estado_patrimonio;
            }
            if( $request->responsavel){
                $patrimonio->id_responsavel = $request->responsavel;
            }
            $patrimonio->save();


            $movimentacao = new Movimentacao();
            $movimentacao->id_patrimonio = $request->id;
            $movimentacao->origem = $request->id_local_antigo;

            if($request->id_local){
                 $movimentacao->destino = $request->id_local;
            }else{
                  $movimentacao->destino = $request->id_local_antigo;
            }

            $movimentacao->antigo_responsavel = $request->responsavel_antigo;
            if($request->responsavel){
               $movimentacao->novo_responsavel = $request->responsavel;
            }else{
                $movimentacao->novo_responsavel = $request->responsavel_antigo;
            }

            $movimentacao->id_estado_antigo = $request->id_estado_antigo;
            if( $request->id_estado_patrimonio){
                $movimentacao->id_estado_novo = $request->id_estado_patrimonio;
            }else{
                $movimentacao->id_estado_novo = $request->id_estado_antigo;
            }

            $movimentacao->motivo = $request->descricao;
            $movimentacao->id_utilizador = Auth::user()->id;
            $movimentacao->save();


            DB::commit();
            return redirect()->route('movimento.patrimonio', $request->id)
                     ->with('success', 'Património movimentado com sucesso!');

        } catch (\Throwable $th) {

            DB::rollBack();
            return redirect()->route('movimento.patrimonio',$request->id)
                    ->with('erro', 'Ocorreu um erro ao movimentar o patrimoónio \n'.$th->getMessage());
        }
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

    public function dados_patrimonio($id){
        $patrimonio =  DB::table('patrimonios as Patr')
            ->select(
                'Patr.id',
                'Patr.nome as nome_patrimonio',
                'Patr.id_localizacao',
                'Patr.id_responsavel',
                'Patr.id_estado_patrimonio',
                'Patr.imagem',
                'Est.nome as estado_patrimonio',
                'Resp.nome as responsavel',
                'Lo.nome as localizacao',
            )
            ->leftJoin('estado_patrimonios as Est', 'Est.id', '=', 'Patr.id_estado_patrimonio')
            ->leftJoin('responsavels as Resp', 'Resp.id', '=', 'Patr.id_responsavel')
            ->leftJoin('locals as Lo', 'Lo.id', '=', 'Patr.id_localizacao')
            ->where('Patr.id', $id)
            ->first();

        $local = Local::find($patrimonio->id_localizacao);
        $patrimonio->caminhoLocal = $local ? $this->caminho($local) : null;

        return $patrimonio;

    }


     //funcao que pega da base de dados todos os patrimonios
    public function dados_movimentacoes(){
        $query = DB::table('movimentacaos as Mov')
            ->select(
                'Mov.id as ordem',
                'Patr.id',
                'Patr.nome',
                'Patr.codigo',
                'Patr.imagem',
                'Patr.origem',
                'Patr.conservacao',
                'Patr.documento',
                'Patr.marca',
                'Patr.cor as cor_patrimonio',
                'Mov.created_at',
                'Patr.num_serie',
                'Cat.nome as categoria',
                'Est.nome as estado_patrimonio',
                'Est2.nome as antigo_estado_patrimonio',
                'Est.cor',
                'Est.background',
                'Resp.nome as responsavel',
                'Resp2.nome as antigo_responsavel',
                'Lo.nome as localizacao',
                'Lo2.nome as antiga_localizacao',
                'Lo.id as id_local',
                'U.name as nome_utilizador',
            )
            ->leftJoin('patrimonios as Patr', 'Patr.id', '=', 'Mov.id_patrimonio')
            ->leftJoin('categorias as Cat', 'Cat.id', '=', 'Patr.id_categoria')
            ->leftJoin('estado_patrimonios as Est', 'Est.id', '=', 'Mov.id_estado_novo')
            ->leftJoin('responsavels as Resp', 'Resp.id', '=', 'Mov.novo_responsavel')
            ->leftJoin('locals as Lo', 'Lo.id', '=', 'Mov.destino')
            ->leftJoin('estado_patrimonios as Est2', 'Est2.id', '=', 'Mov.id_estado_antigo')
            ->leftJoin('responsavels as Resp2', 'Resp2.id', '=', 'Mov.antigo_responsavel')
            ->leftJoin('locals as Lo2', 'Lo2.id', '=', 'Mov.origem')
            ->leftJoin('users as U', 'U.id', '=', 'Mov.id_utilizador')
            ->where('Patr.estado','=', 'activo')
            ->orderBy('Mov.id', 'desc');

        return DataTables::of($query)
            ->addColumn('caminhoLocal', function($patrimonio) {
                $local = Local::find($patrimonio->id_local);
                return $local ? $this->caminho($local) : '';
            })
        ->make(true);
    }

  public function dados_movimentacoes_patr(){
    // IDs únicos de patrimônios com movimentações
    $patrimonioIds = DB::table('movimentacaos as Mov')
        ->join('patrimonios as Patr', 'Patr.id', '=', 'Mov.id_patrimonio')
        ->where('Patr.estado', 'activo')
        ->distinct()
        ->pluck('Patr.id');

    // Subquery para pegar a última movimentação completa de cada patrimônio
    $ultimaMovimentacao = DB::table('movimentacaos as Mov')
        ->whereIn('Mov.id_patrimonio', $patrimonioIds)
        ->whereRaw('Mov.created_at = (SELECT MAX(created_at) FROM movimentacaos WHERE id_patrimonio = Mov.id_patrimonio)')
        ->select(
            'Mov.id_patrimonio',
            'Mov.created_at as ultima_ocorrencia',
            'Mov.motivo',
            'Mov.id_estado_antigo',
            'Mov.antigo_responsavel',
            'Mov.origem as id_local_antigo'
        );

    // Query principal
    return DB::table('patrimonios as Patr')
        ->select(
            'Patr.id',
            'Patr.nome',
            'Patr.codigo',
            'Patr.imagem',
            'Patr.origem',
            'Patr.conservacao',
            'Patr.documento',
            'Patr.marca',
            'Patr.cor as cor_patrimonio',
            'Patr.num_serie',
            'Cat.nome as categoria',
            'Est.nome as estado_patrimonio',
            'Est.cor',
            'Est.background',
            'Resp.nome as responsavel',
            'Lo.nome as localizacao',
            'Lo.id as id_local',
            'MovUlt.ultima_ocorrencia',
            'MovUlt.motivo',
            'Resp2.nome as antigo_responsavel',
            'Lo2.nome as antiga_localizacao',
            'Est2.nome as antigo_estado_patrimonio'
        )
        ->leftJoin('categorias as Cat', 'Cat.id', '=', 'Patr.id_categoria')
        ->leftJoin('estado_patrimonios as Est', 'Est.id', '=', 'Patr.id_estado_patrimonio')
        ->leftJoin('responsavels as Resp', 'Resp.id', '=', 'Patr.id_responsavel')
        ->leftJoin('locals as Lo', 'Lo.id', '=', 'Patr.id_localizacao')
        // LEFT JOIN com a subquery da última movimentação
        ->leftJoinSub($ultimaMovimentacao, 'MovUlt', function($join){
            $join->on('Patr.id', '=', 'MovUlt.id_patrimonio');
        })
        // agora pegando informações antigas via joins usando as colunas da subquery
        ->leftJoin('estado_patrimonios as Est2', 'Est2.id', '=', 'MovUlt.id_estado_antigo')
        ->leftJoin('responsavels as Resp2', 'Resp2.id', '=', 'MovUlt.antigo_responsavel')
        ->leftJoin('locals as Lo2', 'Lo2.id', '=', 'MovUlt.id_local_antigo')
        ->whereIn('Patr.id', $patrimonioIds)
        ->orderByDesc('MovUlt.ultima_ocorrencia')
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

}
