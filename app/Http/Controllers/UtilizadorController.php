<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Services\Helper;


class UtilizadorController extends Controller
{

    protected Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    //Rota, funcao que chama a view de listagem dos utilizadores
    public function index(){
        return Inertia::render('Utilizador/Utilizador',[
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
                ]
            ]);
    }

    //Rota, funcao de registo de utilizador
    public function registar_utilizador(Request $request){
        $validacao = $this->validarUtilizador($request);
        try {

            $this->registarUtilizador($request);
            return redirect()->route('users')
                     ->with('success', 'Utilizador registado com sucesso!');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('users')
                    ->with('erro', 'Ocorreu um erro ao registar o utilizador \n'.$th->getMessage());
        }
    }

    //Rota. funcao eliminar utilizadores
    public function eliminar_utilizador(Request $request){
       try {
            // Eliminar os utilizadores dentro de uma transação
            $this->eliminarUtilizador($request->ids);
            return redirect()->route('users')
                     ->with('success', 'Eliminação bem sucedida!');
        } catch (\Exception $th) {
           return redirect()->route('users')
                    ->with('erro', 'Ocorreu um erro ao eliminar \n'.$th->getMessage());
        }
    }

    //Rota, funcao de editar utilizador
    public function editar_utilizador(Request $request){
        if($request->email_copia_editar == $request->email_editar){ //nao alterou o email
            if($request->senha_editar){ //alterou a senha
                $validacao = $this->validarUtilizadorEditar1($request);
            }else{ //nao alterou a senha
                $validacao = $this->validarUtilizadorEditar2($request);
            }
        }else{ //alterou o emai
            if($request->senha_editar){ //alterou a senha
                $validacao = $this->validarUtilizadorEditar3($request);
            }else{ //nao alterou a senha
                $validacao = $this->validarUtilizadorEditar4($request);
            }
        }
        try {
            $this->editarUtilizador($request);
            return redirect()->route('users')
                     ->with('success', 'Utilizador actualizado com sucesso!');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('users')
                    ->with('erro', 'Ocorreu um erro ao actualizaro utilizador\n'.$th->getMessage());
        }
    }

    //funcao que pega da base de dados todos os utilizadores
    public function dados_utilizadores(){
        $query = User::query()->where('role','!=','Admin'); // apenas a query, não carrega tudo ainda
        return DataTables::of($query)->make(true);
    }

    //Funcao que faz insert dos dados dos utilizadores
    public function registarUtilizador($request){

        DB::beginTransaction();
        $utilizador = new User();
        $utilizador->name = $this->helper->formatarNomeProprio($request->nome);
        $utilizador->contacto = $request->contacto;

        $utilizador->email = $request->email;
        $utilizador->password = Hash::make($request->senha);
        $utilizador->role = 'Gestor';
        $utilizador->save();

        //dando o perfil de operador
        DB::table('model_has_roles')->insert([
            'role_id' => 2, // id da permissão
            'model_type' => \App\Models\User::class,
            'model_id' => $utilizador->id,
        ]);
        DB::commit();
    }

    //funcao eliminar utilizador
    public function eliminarUtilizador($ids){
        DB::transaction(function () use ($ids) {
            User::whereIn('id', $ids)->delete();
        });
    }

    public function editarUtilizador($request){
        $utilizador = User::findOrFail($request->id);
        DB::beginTransaction();
        $utilizador->name = $request->nome_editar;
        $utilizador->contacto = $request->contacto_editar;
        $utilizador->estado = $request->estado;
        if($request->email_copia_edit != $request->email_editar){ //nao alterou o email
            $utilizador->email = $request->email_editar;
        }
        if($request->senha_editar){
            $utilizador->password = Hash::make($request->senha_editar);
        }
        $utilizador->save();
        DB::commit();
    }

    //Funcao que valida os dados do formulario do utilizador
    private function validarUtilizador($request)
    {
        return $request->validate(
            [
                'nome' => ['required', 'min:3', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÂÊÔâêôÃÕãõçÇ\s]+$/'],
                'contacto' => ['required', 'digits_between:9,12'],
                'email' => ['required', 'email','unique:users,email'],
                'senha' => ['required', 'min:6'],
                'confirma_senha' => ['required', 'min:6', 'same:senha'],
            ],
            [
                'nome.required' => 'O nome é obrigatório.',
                'nome.min' => 'O nome deve ter pelo menos 3 caracteres.',
                'nome.regex' => 'O nome deve conter apenas letras e espaços.',

                'contacto.required' => 'O contacto é obrigatório.',
                'contacto.digits_between' => 'O contacto deve ter entre 9 e 12 dígitos.',

                'email.required' => 'O email é obrigatório.',
                'email.email' => 'Informe um email válido.',
                'email.unique' => 'Este email já está registado.',

                'senha.required' => 'A senha é obrigatória.',
                'senha.min' => 'A senha deve ter pelo menos 6 caracteres.',

                'confirma_senha.required' => 'A confirmação da senha é obrigatória.',
                'confirma_senha.same' => 'A confirmação não corresponde à senha.',
            ]
        );
    }

    private function validarUtilizadorEditar1($request)
    {
        return $request->validate(
            [
                'nome_editar' => ['required', 'min:3', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÂÊÔâêôÃÕãõçÇ\s]+$/'],
                'contacto_editar' => ['required', 'digits_between:9,12'],
                'senha_editar' => ['required', 'min:6'],
                'confirma_senha_editar' => ['required', 'min:6', 'same:senha'],
            ],
            [
                'nome_editar.required' => 'O nome é obrigatório.',
                'nome_editar.min' => 'O nome deve ter pelo menos 3 caracteres.',
                'nome_editar.regex' => 'O nome deve conter apenas letras e espaços.',

                'contacto_editar.required' => 'O contacto é obrigatório.',
                'contacto_editar.digits_between' => 'O contacto deve ter entre 9 e 12 dígitos.',

                'senha_editar.required' => 'A senha é obrigatória.',
                'senha_editar.min' => 'A senha deve ter pelo menos 6 caracteres.',

                'confirma_senha_editar.required' => 'A confirmação da senha é obrigatória.',
                'confirma_senha_editar.same' => 'A confirmação não corresponde à senha.',
            ]
        );
    }

    private function validarUtilizadorEditar2($request)
    {
        return $request->validate(
            [
                'nome_editar' => ['required', 'min:3', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÂÊÔâêôÃÕãõçÇ\s]+$/'],
                'contacto_editar' => ['required', 'digits_between:9,12'],
            ],
            [
                'nome_editar.required' => 'O nome é obrigatório.',
                'nome_editar.min' => 'O nome deve ter pelo menos 3 caracteres.',
                'nome_editar.regex' => 'O nome deve conter apenas letras e espaços.',

                'contacto_editar.required' => 'O contacto é obrigatório.',
                'contacto_editar.digits_between' => 'O contacto deve ter entre 9 e 12 dígitos.',
            ]
        );
    }

     private function validarUtilizadorEditar3($request)
    {
        return $request->validate(
            [
                'nome_editar' => ['required', 'min:3', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÂÊÔâêôÃÕãõçÇ\s]+$/'],
                'contacto_editar' => ['required', 'digits_between:9,12'],
                'email_editar' => ['required', 'email','unique:users,email'],
                'senha_editar' => ['required', 'min:6'],
                'confirma_senha_editar' => ['required', 'min:6', 'same:senha'],
            ],
            [
                'nome_editar.required' => 'O nome é obrigatório.',
                'nome_editar.min' => 'O nome deve ter pelo menos 3 caracteres.',
                'nome_editar.regex' => 'O nome deve conter apenas letras e espaços.',

                'contacto_editar.required' => 'O contacto é obrigatório.',
                'contacto_editar.digits_between' => 'O contacto deve ter entre 9 e 12 dígitos.',

                'email_editar.required' => 'O email é obrigatório.',
                'email_editar.email' => 'Informe um email válido.',
                'email_editar.unique' => 'Este email já está registado.',

                'senha_editar.required' => 'A senha é obrigatória.',
                'senha_editar.min' => 'A senha deve ter pelo menos 6 caracteres.',

                'confirma_senha_editar.required' => 'A confirmação da senha é obrigatória.',
                'confirma_senha_editar.same' => 'A confirmação não corresponde à senha.',
            ]
        );
    }

    private function validarUtilizadorEditar4($request)
    {
        return $request->validate(
            [
                'nome_editar' => ['required', 'min:3', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÂÊÔâêôÃÕãõçÇ\s]+$/'],
                'contacto_editar' => ['required', 'digits_between:9,12'],
                'email_editar' => ['required', 'email','unique:users,email'],
            ],
            [
                'nome_editar.required' => 'O nome é obrigatório.',
                'nome_editar.min' => 'O nome deve ter pelo menos 3 caracteres.',
                'nome_editar.regex' => 'O nome deve conter apenas letras e espaços.',

                'contacto_editar.required' => 'O contacto é obrigatório.',
                'contacto_editar.digits_between' => 'O contacto deve ter entre 9 e 12 dígitos.',

                'email_editar.required' => 'O email é obrigatório.',
                'email_editar.email' => 'Informe um email válido.',
                'email_editar.unique' => 'Este email já está registado.',
            ]
        );
    }
}
