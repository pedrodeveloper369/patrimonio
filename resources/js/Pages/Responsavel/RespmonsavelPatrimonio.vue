<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, onMounted, computed, onUnmounted, watch  } from "vue";
import axios from 'axios';
import { useForm , usePage ,router} from '@inertiajs/vue3';
import Swal from 'sweetalert2'

const movimentacoes = ref([]);
const patrimonios = ref([]);

const props = defineProps({
    id_patrimonio: Number,
    patrim: Object
});

const id_patrimonio = ref(props.id_patrimonio);
const patrim = ref(props.patrim);

//const movimentacoes = ref(usePage().props.value.query);  caso os dados sao passados diretos na view
// Busca os dados do Laravel via rota relativa
onMounted(async () => {
  listar_movimentacoes();
});

const listar_movimentacoes = async () => {
  try {
    const response = await axios.get(`/historico-movimentacoes/${id_patrimonio.value}`)
    patrimonios.value = response.data.data || response.data
  } catch (error) {
    console.error('Erro ao carregar movimentacoes:', error)
  }
}



window.chamar_pagina_movimentar = () => {
  router.visit(route('movimento.patrimonio',id_patrimonio.value));
};

</script>

<template>
    <AuthenticatedLayout>

        <h4 class="fw-bold py-3 mb-4"><Link :href="route('movimentacao')" class="text-muted fw-light">Movimentações/</Link><strong>Histórico das Movimentações</strong></h4>

        <div class="card  p-4 mb-2">
            <div class="">
                <h5>Descrição do Património</h5>
                    <div class="row">
                        <div class="col-md-2">
                            <img
                                v-if="patrim.imagem"
                                :src="`/storage/patrimonios/imagens/${patrim.imagem}`"
                                alt="Imagem do Património"
                                style="width:150px; height:auto; border-radius:9px"
                            >
                            <img
                                v-else
                                src="/assets/img/avatars/pitruca.webp"
                                alt="Imagem padrão"
                                style="width:150px; height:auto; border-radius:9px"
                            >
                        </div>
                        <div class="col-md-10">
                            <h6 > <strong>Nome:</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{patrim.nome_patrimonio}}</h6>
                            <h6 > <strong>Código:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{patrim.codigo}}</h6>
                            <h6 ><strong>Estado actual:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{patrim.estado_patrimonio}}</h6>
                            <h6 ><strong>Responsavel actual:</strong> &nbsp;&nbsp;&nbsp;{{patrim.responsavel}}</h6>
                            <h6 ><strong>Localização actual:</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{patrim.localizacao}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;({{patrim.caminhoLocal}})</h6>

                        </div>
                    </div>
            </div>
        </div>
        <div class="card p-4 ">

            <div class="table-responsive text-nowrap mt-3">

                 <table
                        v-datatable="{ selectable: false,datatableOptions, defaultPageSize: 10,
                        deleteAction: (selectedIds) => {
                            //chama modal
                            openDeleteModal(selectedIds);
                        },
                        actionsHtml: `
                            <button onclick='window.chamar_pagina_movimentar()'  class='btn btn-primary btn-sm' id='btn-add'> <i class='bx bx-transfer me-2'></i> Movimentar</button>

                        `
                        }"
                    @selection-changed="onSelectionChanged"
                    @datatable-delete="onDeleteRequested"
                     @datatable-action="onDatatableAction"
                    class="table table-hover table-striped mt-3 min-w-full  mt-6 text-sm"
                >
                    <thead>
                      <tr>
                        <th>Responsável</th>
                        <th>Localização</th>
                        <th>Estado</th>
                        <th>Motivo</th>
                        <th>Movimentado Por</th>
                        <th>Movimentado Em</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <tr v-for="mov in patrimonios" :key="mov.id" :data-id="mov.id">
                            <td class="p-3">{{ mov.responsavel}}</td>
                            <td>{{mov.localizacao}} ({{mov.caminhoLocal}})</td>
                            <td>
                               <span
                                    class="px-2 py-1 text-xs font-semibold rounded"
                                        :style="{ backgroundColor: mov.background, color: mov.cor }"

                                >
                                    {{ mov.estado_patrimonio }}
                                </span>
                            </td>
                            <td>{{mov.motivo }}</td>
                            <td>{{mov.nome_utilizador }}</td>
                            <td>{{ new Date(mov.created_at).toLocaleDateString() }}</td>
                        </tr>
                    </tbody>
                  </table>
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



