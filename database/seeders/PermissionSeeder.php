<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //DASHBOARD 4
        /*1*/Permission::create(['name' => 'ver_dashboard']);
        /*2*/Permission::create(['name' => 'ver_dashboard_admin']);
        /*3*/Permission::create(['name' => 'ver_estatisticas_admin']);
        /*4*/Permission::create(['name' => 'ver_estatisticas']);


        //MÓDULO DE AUTENTICAÇÃO & UTILIZADORES 10
        /*5*/Permission::create(['name' => 'utilizadores']);
        /*6*/Permission::create(['name' => 'ver_utilizadores']);
        /*7*/Permission::create(['name' => 'cadastrar_utilizador']);
        /*8*/Permission::create(['name' => 'editar_utilizador']);
        /*9*/Permission::create(['name' => 'eliminar_utilizador']);
        /*10*/Permission::create(['name' => 'ver_perfis']);
        /*11*/Permission::create(['name' => 'ver_perfil_proprio']);
        /*12*/Permission::create(['name' => 'editar_perfil_proprio']);

        //MÓDULO DE UNIDADES 7
        /*13Permission::create(['name' => 'unidades']);
        /*14*Permission::create(['name' => 'ver_unidades']);
        /*15*Permission::create(['name' => 'cadastrar_unidade']);
        /*16*Permission::create(['name' => 'editar_unidade']);
        /*17*Permission::create(['name' => 'eliminar_unidade']);
        /*18*Permission::create(['name' => 'ver_relatorios_unidade']);
        /*19*Permission::create(['name' => 'ver_patrimonio_unidade']);*/

        //MÓDULO DE CATEGORIAS DE PATRIMÓNIO 7
        /*13*/Permission::create(['name' => 'categorias']);
        /*14*/Permission::create(['name' => 'ver_categorias']);
        /*15*/Permission::create(['name' => 'cadastrar_categoria']);
        /*16*/Permission::create(['name' => 'editar_categoria']);
        /*17*/Permission::create(['name' => 'eliminar_categoria']);

        //MÓDULO DE RESPONSÁVEIS 8
        /*18*/Permission::create(['name' => 'responsaveis']);
        /*19*/Permission::create(['name' => 'ver_responsaveis']);
        /*20*/Permission::create(['name' => 'cadastrar_responsavel']);
        /*21*/Permission::create(['name' => 'editar_responsavel']);
        /*22*/Permission::create(['name' => 'eliminar_responsavel']);
        /*23*/Permission::create(['name' => 'atribuir_patrimonio_responsavel']);
        /*24*/Permission::create(['name' => 'trasnferir_patrimonio_responsavel']);
        /*25*/Permission::create(['name' => 'ver_patrimonio_responsavel']);

        //MÓDULO DE PATRIMÓNIO 5
        /*26*/Permission::create(['name' => 'patrimonio']);
        /*27*/Permission::create(['name' => 'cadastrar_patrimonio']);
        /*28*/Permission::create(['name' => 'ver_patrimonios']);
        /*29*/Permission::create(['name' => 'editar_patrimonio']);
        /*30*/Permission::create(['name' => 'eliminar_patrimonio']);

        //MÓDULO DE MOVIMENTAÇÃO DO PATRIMÓNIO 8
        /*31*/Permission::create(['name' => 'movimentacoes']);
        /*32*/Permission::create(['name' => 'ver_movimentacoes']);
        /*33*/Permission::create(['name' => 'editar_movimentacao']);
        /*34*/Permission::create(['name' => 'eliminar_movimentacao']);
        /*35*/Permission::create(['name' => 'historico_movimentacoes']);
        /*36*/Permission::create(['name' => 'movimentar_responsavel']);

        //MÓDULO DE RELATÓRIOS 8
        /*37*/Permission::create(['name' => 'relatorios']);
        /*38*/Permission::create(['name' => 'ver_relatorios']);
        /*39*/Permission::create(['name' => 'relatorio_por_categoria']);
        /*40*/Permission::create(['name' => 'relatorio_estado_patrimonio']);
        /*41*/Permission::create(['name' => 'relatorio_patrimonio_movimentado']);
        /*42*/Permission::create(['name' => 'exportar_relatorios_pdf']);

        //MÓDULO DE CONFIGURAÇÕES DO GRUPO 4
        /*43*/Permission::create(['name' => 'Configuracoes']);
        /*44*/Permission::create(['name' => 'ver_empresa']);
        /*45*/Permission::create(['name' => 'editar_empresa']);
        /*46*/Permission::create(['name' => 'gerir_dados_empresa']);

        //MÓDULO DE DEPARTAMENTO
        /*47*/Permission::create(['name' => 'departamento']);
        /*48*/Permission::create(['name' => 'criar_departamento']);
        /*49*/Permission::create(['name' => 'editar_departamento']);
        /*50*/Permission::create(['name' => 'eliminar_departamento']);

        //MÓDULO DE CARGO
        /*51*/Permission::create(['name' => 'cargo']);
        /*52*/Permission::create(['name' => 'criar_cargo']);
        /*53*/Permission::create(['name' => 'editar_cargo']);
        /*54*/Permission::create(['name' => 'eliminar_cargo']);

    }
}
