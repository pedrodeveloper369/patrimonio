<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, onMounted, computed, onUnmounted, watch  } from "vue";
import axios from 'axios';
import { useForm , usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2'

const movimentacoes = ref([]);
const patrimonios = ref([]);


//const movimentacoes = ref(usePage().props.value.query);  caso os dados sao passados diretos na view
// Busca os dados do Laravel via rota relativa
onMounted(async () => {
  listar_movimentacoes();
  listar_movimentacoes_patrimonio();
});

const listar_movimentacoes = async () => {
  try {
    const response = await axios.get('/movimentacoes/dados')
    movimentacoes.value = response.data.data || response.data
  } catch (error) {
    console.error('Erro ao carregar movimentacoes:', error)
  }
}

const listar_movimentacoes_patrimonio = async () => {
  try {
    const response = await axios.get('/movimentacoes/dados/patrimonio')
    patrimonios.value = response.data.data || response.data
  } catch (error) {
    console.error('Erro ao carregar movimentacoes:', error)
  }
}

//ver detalhes
function ver_detalhes(patrimonio){
    document.getElementById('nome').innerText = patrimonio.nome;
    document.getElementById('codigo').innerText = patrimonio.codigo;
    document.getElementById('categoria').innerText = patrimonio.categoria;
    document.getElementById('estado').innerText = patrimonio.estado_patrimonio;
    document.getElementById('responsavel').innerText = patrimonio.responsavel;
    document.getElementById('localizacao').innerText = patrimonio.localizacao;

    document.getElementById('antigo_estado').innerText = patrimonio.antigo_estado_patrimonio;
    document.getElementById('antigo_responsavel').innerText = patrimonio.antigo_responsavel;
    document.getElementById('antiga_localizacao').innerText = patrimonio.antiga_localizacao;
    document.getElementById('data_movimentacao').innerText = patrimonio.ultima_ocorrencia;
    document.getElementById('motivo').innerText = patrimonio.motivo;

}

</script>

<template>
    <AuthenticatedLayout>
        <h4 class=""><strong>Movimentações</strong></h4>

        <div class="card p-4 ">
            <ul class="nav nav-tabs nav-fill" role="tablist">
                <li class="nav-item">
                <button
                    type="button"
                    class="nav-link active"
                    role="tab"
                    data-bs-toggle="tab"
                    data-bs-target="#navs-justified-profile"
                    aria-controls="navs-justified-profile"
                    aria-selected="false"
                >
                   <i class="bx bx-archive me-2"></i> Patrimónios Movimentados
                </button>
                </li>
                <li class="nav-item">
                <button
                    type="button"
                    class="nav-link "
                    role="tab"
                    data-bs-toggle="tab"
                    data-bs-target="#navs-justified-home"
                    aria-controls="navs-justified-home"
                    aria-selected="true"
                >
                    <i class="bx bx-transfer me-2"></i> Todas Movimentações
                </button>
                </li>

            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="navs-justified-profile" role="tabpanel">

                    <div class="table-responsive text-nowrap mt-3">
                          <table v-datatable="{selectable: false,datatableOptions, defaultPageSize: 10,
                                deleteAction: (selectedIds) => {
                                    //chama modal
                                    openDeleteModal(selectedIds);
                                },
                                actionsHtml: `

                                `
                                }"
                            @selection-changed="onSelectionChanged"
                            @datatable-delete="onDeleteRequested"
                                @datatable-action="onDatatableAction"
                            class="table table-hover table-striped mt-3 min-w-full  mt-6 text-sm"
                        >

                        <thead class="bg-gray-100 ">
                            <tr>
                                <th>Imagem</th>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th>Responsável</th>
                                <th>Localização</th>
                                <th>Estado</th>
                                <th>Movimentado Em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="mov in patrimonios" :key="mov.id" :data-id="mov.id">
                                 <td>
                                    <img
                                        v-if="mov.imagem"
                                        :src="`/storage/patrimonios/imagens/${mov.imagem}`"
                                        alt="Imagem do Património"
                                        style="width:50px; height:auto; border-radius:9px"
                                    >
                                    <img
                                        v-else
                                        src="/assets/img/avatars/pitruca.webp"
                                        alt="Imagem padrão"
                                        style="width:50px; height:auto; border-radius:9px"
                                    >
                                </td>

                                <td  class="p-3" ><strong style="color: #212529 !important;">{{ mov.nome }} </strong> <br></td>
                                <td>{{ mov.categoria }}</td>
                                <td>{{ mov.responsavel }}</td>
                                <td class="date-cell">{{ mov.localizacao}}</td>

                                <td >
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded"
                                            :style="{ backgroundColor: mov.background, color: mov.cor }"

                                    >
                                        {{ mov.estado_patrimonio }}
                                    </span>

                                </td>

                                <td class="date-cell">{{ new Date(mov.ultima_ocorrencia).toLocaleDateString() }}</td>

                                <td>
                                    <button class="" @click="ver_detalhes(mov)"   data-bs-toggle='modal' data-bs-target='#modalDetalhes' ><i class="menu-icon bx bx-show"></i></button>

                                    <Link :href="route('movimento.patrimonio', mov.id)" style="color:#777"> <i class="bx bx-transfer me-2"></i> </Link>
                                    <Link :href="route('movimento.historico', mov.id)" style="color:#777"> <i class="bx bx-list-ul"></i></Link>

                                </td>

                            </tr>
                        </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade " id="navs-justified-home" role="tabpanel">
                    <div class="table-responsive text-nowrap mt-3">
                        <table v-datatable="{selectable: false, datatableOptions, defaultPageSize: 10,
                                deleteAction: (selectedIds) => {
                                    //chama modal
                                    openDeleteModal(selectedIds);
                                },
                                actionsHtml: `

                                `
                                }"
                            @selection-changed="onSelectionChanged"
                            @datatable-delete="onDeleteRequested"
                                @datatable-action="onDatatableAction"
                            class="table table-hover table-striped mt-3 min-w-full  mt-6 text-sm"
                        >

                        <thead class="bg-gray-100 ">
                            <tr>


                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Responsável</th>
                            <th>Localização</th>
                            <th>Estado</th>
                             <th>Movimentado Por</th>
                            <th>Movimentado Em</th>

                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="mov in movimentacoes" :key="mov.id" :data-id="mov.id">

                            <td  class="p-3" ><strong style="color: #212529 !important;" >{{ mov.nome }} </strong> </td>
                            <td>{{ mov.categoria }}</td>
                            <td>{{ mov.responsavel }}</td>
                            <td class="date-cell">{{ mov.localizacao}}</td>

                            <td >
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded"
                                        :style="{ backgroundColor: mov.background, color: mov.cor }"

                                >
                                    {{ mov.estado_patrimonio }}
                                </span>

                            </td>
                            <td>{{ mov.nome_utilizador }}</td>
                            <td class="date-cell">{{ new Date(mov.created_at).toLocaleDateString() }}</td>



                            </tr>
                        </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!--MODAL DETALHE-->
        <div class="modal fade" id="modalDetalhes" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="border:1px solid #debbb3">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel3">Detallhes da Ultima Movimentação</h5>
                        <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">

                        <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label">Nome</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="nome"></label>
                            </div>
                        </div>
                          <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label">Código</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="codigo"></label>
                            </div>
                        </div>


                         <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label" >Categoria</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="categoria"></label>
                            </div>
                        </div>
                         <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label" >Antigo Responsavel</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="antigo_responsavel"></label>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label" >Responsavel Actual</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="responsavel"></label>
                            </div>
                        </div>
                         <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label" >Antigo Estado</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="antigo_estado"></label>
                            </div>
                        </div>
                         <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label">Estado Actual</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="estado"></label>
                            </div>
                        </div>


                         <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label">Antiga Localização</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="antiga_localizacao"></label>
                            </div>
                        </div>
                         <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label">Localização Actual</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="localizacao"></label>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label" >Data da Movimentação</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="data_movimentacao"></label>
                            </div>
                        </div>

                         <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label">Motivo da Movimentação</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="motivo"></label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<style scoped>
.select-icon-wrapper {
    position: relative;
    width: 100%;
}

.select-icon-wrapper select {
    padding-left: 32px !important; /* espaço para o ícone */
}

.select-icon-wrapper .icon {
    position: absolute;
    left: 15px;
    top: 60%;
    transform: translateY(-50%);
    font-size: 16px;
    color: #6c757d; /* cinza bootstrap */
    pointer-events: none;
}

.equal-height {
    height: 32px; /* igual ao form-select-sm */
}

</style>



