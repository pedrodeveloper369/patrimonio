<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ConfiguracaoController extends Controller
{
    public function index(){
        return Inertia::render('Configuracao/Configuracao',[
            'utilizador' => User::where('id','=',Auth::id())->first(),
            'flash' => [
                'success' => session('success'),
                'erro' => session('erro'),
            ]
        ]);
    }

    public function actualizar_perfil(Request $request){
        $this->validarConfiguracaoPerfil($request);
        try {
            $utilizador = User::findOrFail(Auth::id());
            DB::beginTransaction();
            $utilizador->name = $request->nome;
            $utilizador->contacto = $request->contacto;
            $utilizador->save();
            DB::commit();

            return redirect()->route('configuracao')
                     ->with('success', 'Dados actualizados com sucesso!');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('configuracao')
                    ->with('erro', $th->getMessage());
        }
    }

     public function actualizar_definicao(Request $request){
        $this->validarConfiguracaoDefinicao($request);
        try {
            $utilizador = User::findOrFail(Auth::id());
            DB::beginTransaction();
            $utilizador->email = $request->email;
            $utilizador->password = Hash::make($request->senha);
            $utilizador->save();
            DB::commit();

            return redirect()->route('configuracao')
                     ->with('success', 'Dados actualizados com sucesso!');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('configuracao')
                    ->with('erro', $th->getMessage());
        }
    }

    //Funcao que valida os dados do formulario do utilizador
    private function validarConfiguracaoPerfil($request)
    {

        return $request->validate(
            [
                'nome' => ['required', 'min:3', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÂÊÔâêôÃÕãõ\s]+$/'],
                'contacto' => ['required', 'digits_between:9,12'],
            ],
            [
                'nome.required' => 'O nome é obrigatório.',
                'nome.min' => 'O nome deve ter pelo menos 3 caracteres.',
                'nome.regex' => 'O nome deve conter apenas letras e espaços.',

                'contacto.required' => 'O contacto é obrigatório.',
                'contacto.digits_between' => 'O contacto deve ter entre 9 e 12 dígitos.',
            ]
        );
    }

    //Funcao que valida os dados do formulario do utilizador
    private function validarConfiguracaoDefinicao($request)
    {
        return $request->validate(
            [
                'email' => ['required', 'email','unique:users,email'],
                'senha' => ['required', 'min:6'],
                'confirma_senha' => ['required', 'min:6', 'same:senha'],
            ],
            [
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
}
