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
        return Inertia::render('Local/Local', [
            'tipoLocal' => TipoLocal::orderBy('nome')->get(),
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
            ],
        ]);
    }

    public function index_registar(){
        return Inertia::render('Local/RegistarLocal',[
            'tipoLocal' => TipoLocal::orderBy('nome', 'asc')->get(),
            'caminhosLocal' => $this->caminhos_organizados(),
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
            ],
        ]);
    }

    public function index_editar($id_local){
       return Inertia::render('Local/EditarLocal',[
            'tipoLocal' => TipoLocal::orderBy('nome', 'asc')->get(),
            'caminhosLocal' => $this->caminhos_organizados(),
            'local' => collect($this->montarHierarquiaCompleta($this->buscar_local()))->firstWhere('id', $id_local),
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
            ],
        ]);
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

            return redirect()->route('registar.local')->with('success', 'local registado com sucesso!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('registar.local')->with('erro', 'Ocorreu um erro ao registar o local \n'.$th->getMessage());
        }
    }

    public function editar_local(Request $request){
        $validacao = $this->validarlocal($request);

         try {
            $local = Local::findOrFail($request->id);

            DB::beginTransaction();
            $local->nome = $this->helper->formatarNomeProprio($request->nome);
            $local->id_tipolocal = $request->tipoLocalizacao;
            $local->estado = $request->estado;
            $local->parent_id = $request->id_local; //este é o id do espaco geral onde o local a ser cadastrado pertencerá
            $local->save();
            DB::commit();
            return redirect()->route('editar.local',$request->id)
                     ->with('success', 'local actualizado com sucesso!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('editar.local',$request->id)
                    ->with('erro', 'Ocorreu um erro ao actualizar o local \n'.$th->getMessage());
        }
    }

    //funcao que pega da base de dados todos os locals
    public function buscar_local(){
        return DB::table('locals')
            ->join('tipo_locals', 'tipo_locals.id', '=', 'locals.id_tipolocal')
            ->leftJoin('locals as parent', 'parent.id', '=', 'locals.parent_id')
            ->select(
                'locals.id',
                'locals.nome',
                'locals.estado',
                'locals.parent_id',
                'tipo_locals.nome as tipo',
                'tipo_locals.id as id_tipolocal',
                DB::raw('COALESCE(parent.nome, "—") as localizacao')
            )
            ->get();
    }

    public function dados_local()
    {
        return $this->montarHierarquiaCompleta($this->buscar_local());
    }

    private function montarHierarquiaCompleta($locais)
    {
        // indexa os locais por ID
        $map = [];
        foreach ($locais as $local) {
            $map[$local->id] = $local;
        }

        // função recursiva: sobe até à raiz
        $getCaminho = function ($local) use (&$getCaminho, $map) {
            if (!$local || !$local->parent_id || !isset($map[$local->parent_id])) {
                return $local?->nome ?? null;
            }
            return $getCaminho($map[$local->parent_id]) . ' → ' . $local->nome;
        };

        return $locais->map(function ($local) use ($getCaminho, $map) {
            // começa no PAI, não no próprio
            $caminho = null;

            if ($local->parent_id && isset($map[$local->parent_id])) {
                $caminho = $getCaminho($map[$local->parent_id]);
            }

            return [
                'id' => $local->id,
                'nome' => $local->nome,
                'tipo' => $local->tipo,
                'estado' => $local->estado,
                'id_tipolocal' => $local->id_tipolocal,
                'parent_id' => $local->parent_id,
                'localizacao' => $local->localizacao, // pai direto
                'caminho_completo' => $caminho ?? '—',
            ];
        });
    }

    /*
    private function montarHierarquiaCompleta2($locais)
    {
        // transforma em array indexado por ID (rápido)
        $map = [];
        foreach ($locais as $local) {
            $map[$local->id] = $local;
        }

        // função recursiva
        $getCaminho = function ($local) use (&$getCaminho, $map) {
            if (!$local->parent_id || !isset($map[$local->parent_id])) {
                return $local->nome;
            }

            return $getCaminho($map[$local->parent_id]) . ' → ' . $local->nome;
        };

        // adiciona o campo caminho_completo
        return $locais->map(function ($local) use ($getCaminho) {
            return [
                'id' => $local->id,
                'nome' => $local->nome,
                'tipo' => $local->tipo,
                'id_tipolocal' => $local->id_tipolocal,
                'parent_id' => $local->parent_id,
                'localizacao' => $local->localizacao, // pai direto (como já tens)
                'caminho_completo' => $getCaminho($local), // 🔥 NOVO
            ];
        });
    }*/

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

                'tipoLocalizacao.required' => 'O tipo de localização é obrigatório.',
            ]
        );
    }
}
