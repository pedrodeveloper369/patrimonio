<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, onMounted, computed, onUnmounted, watch  } from "vue";
import axios from 'axios';
import { useForm , usePage, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2'

const responsavel = ref([]);

//pegando dados vindo do controller
const props = defineProps({
  tipoLocal: Array,
});
const tipoLocal = ref(props.tipoLocal);


//declaracao do formulario e os seus dados
const form = useForm({
    nome: '',
    contacto: '',
    departamento: '',
    cargo: '',
})

const formEditar = useForm({
    id: '',
    nome: '',
    contacto: '',
    departamento: '',
    cargo: '',
})

const formEliminar = useForm({
    ids: []
})

// Função para enviar
const submit = () => {
    form.post(route('responsavel.registar'), {
        onSuccess: () => {
            $('#modalRegistar').modal('hide');
            resetModal()       // reseta o formulário
            listar_responsaveis()  //recarrega a tabela
        }
    })
}

//mensagens de registo
const page = usePage()
watch(() => page.props.flash.erro, (msg) => {
  if (msg) {
    Swal.fire({
      toast: true,          // transforma em notificação estilo toast
      position: 'top-end',  // canto direito superior
      icon: 'error',
      title: msg,
      showConfirmButton: false, // sem botão de confirmação
      timer: 6000,          // desaparece após 3 segundos
      timerProgressBar: true,
    })
  }
})

// Mensagem de sucesso
watch(() => page.props.flash.success, (msg) => {
  if (msg) {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: msg,
      showConfirmButton: false,
      timer: 6000,
      timerProgressBar: true,
    })
  }
})


// Função para resetar o formulário
function resetModal() {
    form.reset()
    form.clearErrors()
}

onMounted(() => {
    const modalEl = document.getElementById('modalRegistar')
    modalEl.addEventListener('hidden.bs.modal', resetModal)
})

//filtros computed
const filterLocal = ref('')

//funcao que pesquisa os filtros, pega a lista de dados, merge com uma nova lista de modo a fazer funcionar os
// filtros e a nova lista é usada na tabela
const filteredLocalização = computed(() => {
  return responsavel.value.filter(resp => {
    const matchesLocal = !filterLocal.value || resp.departamento === filterLocal.value
    return matchesLocal
  })
})


function handleDelete(ids) {
  responsavel.value = responsavel.value.filter(u => !ids.includes(u.id));
}

function handleRowAction({ action, row }) {
  console.log(action, row);
}

//const responsavel = ref(usePage().props.value.query);  caso os dados sao passados diretos na view
// Busca os dados do Laravel via rota relativa
onMounted(async () => {
  listar_responsaveis()
});

const listar_responsaveis = async () => {
  try {
    const response = await axios.get('/responsavel/dados')
    responsavel.value = response.data.data || response.data
  } catch (error) {
    console.error('Erro ao carregar responsaveis:', error)
  }
}

//eliminar registo da tabela
const deletingIds = ref([]);
const selectedToDelete = ref([]);      // IDs
const deleteMessage = ref("");         // Mensagem que a modal vai mostrar
const showDeleteModal = ref(false);    // Controla a modal

const submitEliminar = () => {
    formEliminar.post(route('responsavel.eliminar'), {
        onSuccess: () => {
            $('#modalEliminar').modal('hide');
            listar_responsaveis()  //recarrega a tabela
        }
    })
}

function openDeleteModal(ids) {
    deletingIds.value = ids;
    formEliminar.ids = ids;
    $('#modalEliminar').modal('show');
}

//ver detalhes
function ver_detalhes(responsavel){
    document.getElementById('nome').innerText = responsavel.nome;
    document.getElementById('contacto').innerText = responsavel.contacto;
    document.getElementById('departamento').innerText = responsavel.departamento;
    document.getElementById('cargo').innerText = responsavel.cargo;
    document.getElementById('data_registo').innerText = responsavel.created_at;
}

//editar responsavel
function editar_responsavel(responsavel){
    formEditar.reset(); // limpa tudo corretamente
    formEditar.id = responsavel.id;
    formEditar.nome = responsavel.nome;
    formEditar.contacto = responsavel.contacto;
    formEditar.departamento = responsavel.id_departamento;
    formEditar.cargo = responsavel.id_cargo;
}

// Função para enviar
const submitEditar = () => {
    formEditar.post(route('responsavel.editar'), {
        onSuccess: () => {
            $('#modalEditar').modal('hide');
            resetModal()       // reseta o formulário
            listar_responsaveis()  //recarrega a tabela
        }
    })
}

//para rota
window.chamar_pagina_registar_local = () => {
  router.visit(route('registar.local'));
};

</script>

<template>
    <AuthenticatedLayout>
        <h4 class=""><strong>Localizações </strong></h4>
         <div class="card  p-4 mb-2">

           <div class="d-flex flex-column flex-md-row gap-2 w-100">

                <!-- Status -->
                <div class="select-icon-wrapper equal-height">
                    <i class="bx bx-sitemap icon"></i>
                    <select v-model="filterLocal" class="form-select form-select-sm">
                        <option value="">Tipo de Localização</option>
                        <option
                            v-for="tlocal in tipoLocal"
                            :key="tlocal.id"
                            :value="tlocal.nome"
                        >
                            {{ tlocal.nome }}
                        </option>
                    </select>
                </div>

            </div>


        </div>

        <div class="card p-4 ">

            <div class="table-responsive text-nowrap mt-3">
            <table v-datatable="{datatableOptions, defaultPageSize: 10,
                    deleteAction: (selectedIds) => {
                        //chama modal
                        openDeleteModal(selectedIds);
                    },
                    actionsHtml: `
                        <button onclick='window.chamar_pagina_registar_local()'    class='btn btn-primary btn-sm' id='btn-add' ><i class='menu-icon bx bx-plus'></i> Adicionar</button>


                    `
                    }"
                @selection-changed="onSelectionChanged"
                @datatable-delete="onDeleteRequested"
                class="table table-hover mt-3 min-w-full  mt-6 text-sm"
            >

            <thead class="bg-gray-100 ">
                <tr>
                <th></th>
                <th>Nome</th>
                <th>Tipo</th>
                <th>Localização</th>
                <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="resp in filteredLocalização" :key="resp.id" :data-id="resp.id">
                <td></td>
                <td class="p-3" ><strong style="color: #212529 !important;">{{ resp.nome }} </strong> </td>
                <td>{{ resp.contacto }}</td>

                <td>{{ resp.cargo }}</td>



                <td>
                    <button class="" @click="ver_detalhes(resp)"   data-bs-toggle='modal' data-bs-target='#modalDetalhes' ><i class="menu-icon bx bx-show"></i></button>
                    <button class=""  @click="editar_responsavel(resp)"  data-bs-toggle='modal' data-bs-target='#modalEditar'><i class="menu-icon bx bx-edit-alt"></i></button>
                </td>
                </tr>
            </tbody>
            </table>
            </div>

        </div>


        <!--MODAL ELIMINAR-->
        <div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Eliminação</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form @submit.prevent="submitEliminar">
                        <input type="hidden" v-model="formEliminar.ids">

                        <div class="modal-body">
                            Tem certeza que deseja eliminar {{ deletingIds.length }} itens?
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                            <button class="btn btn-danger btn-sm" type="submit">
                                 {{ submitEliminar.processing ? 'Eliminando...' : 'Eliminar' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>


        <!--MODAL REGISTAR-->
        <div class="modal fade" id="modalRegistar" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel3">Registar Localização</h5>
                        <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                        ></button>
                    </div>
                    <form @submit.prevent="submit">
                        <div class="modal-body">

                                <div class="row" >
                                    <div class="col mb-3">
                                        <label for="nameLarge" class="">Nome</label>
                                        <input type="text" v-model="form.nome" class="form-control" placeholder="Enter Name" />
                                        <div v-if="form.errors.nome" class="text-red-500 text-sm mt-1">
                                            {{ form.errors.nome }}
                                        </div>
                                    </div>

                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col mb-0">
                                        <label for="emailLarge" class="">Tipo de Localização</label>
                                       <select v-model="form.departamento" class="form-select">
                                            <option value="">Seleccione o tipo de localização</option>

                                            <option
                                                v-for="tlocal in tipoLocal"
                                                :key="tlocal.id"
                                                :value="tlocal.id"
                                            >
                                                {{ tlocal.nome }}
                                            </option>

                                        </select>


                                        <div v-if="form.errors.departamento" class="text-red-500 text-sm mt-1">
                                            {{ form.errors.departamento }}
                                        </div>
                                    </div>
                                </div>
                                 <div class="row  mb-3">
                                    <div class="col mb-0">
                                        <label for="emailLarge" class="">Localizacao</label>
                                        <input type="text" v-model="form.contacto" class="form-control" placeholder="9xxxxxxxx" />
                                        <div v-if="form.errors.contacto" class="text-red-500 text-sm mt-1">
                                            {{ form.errors.contacto }}
                                        </div>

                                    </div>

                                </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary " data-bs-dismiss="modal">
                            Fechar
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                {{ form.processing ? 'Enviando...' : 'Salvar' }}
                            </button>
                        </div>
                     </form>
                </div>
            </div>
        </div>

        <!--MODAL EDITAR-->
        <div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel3">Actualizar Localização</h5>
                        <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                        ></button>
                    </div>
                    <form @submit.prevent="submitEditar">
                        <div class="modal-body">

                                <div class="row" >
                                    <div class="col mb-3">
                                        <label for="nameLarge" class="">Nome</label>
                                        <input type="text" v-model="formEditar.nome" class="form-control" placeholder="Enter Name" />
                                        <div v-if="formEditar.errors.nome" class="text-red-500 text-sm mt-1">
                                            {{ formEditar.errors.nome }}
                                        </div>
                                    </div>

                                </div>
                                <div class="row  mb-3">
                                    <div class="col mb-0">
                                        <label for="emailLarge" class="">Contacto</label>
                                        <input type="text" v-model="formEditar.contacto" class="form-control" placeholder="9xxxxxxxx" />
                                        <div v-if="formEditar.errors.contacto" class="text-red-500 text-sm mt-1">
                                            {{ formEditar.errors.contacto }}
                                        </div>

                                    </div>

                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col mb-0">
                                        <label for="emailLarge" class="">Departamento</label>
                                       <select v-model="formEditar.departamento" class="form-select">
                                            <option value="">Seleccione o Departamento</option>

                                            <option
                                                v-for="dep in departamentos"
                                                :key="dep.id"
                                                :value="dep.id"
                                            >
                                                {{ dep.nome }}
                                            </option>

                                        </select>


                                        <div v-if="formEditar.errors.departamento" class="text-red-500 text-sm mt-1">
                                            {{ formEditar.errors.departamento }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col mb-0">
                                        <label for="emailLarge" class="">Cargo</label>
                                         <select v-model="formEditar.cargo" class="form-select ">
                                            <option value="">Seleccione o Cargo</option>

                                            <option
                                                v-for="dep in cargos"
                                                :key="dep.id"
                                                :value="dep.id"
                                            >
                                                {{ dep.nome }}
                                            </option>
                                        </select>

                                        <div v-if="formEditar.errors.cargo" class="text-red-500 text-sm mt-1">
                                            {{ formEditar.errors.cargo }}
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary " data-bs-dismiss="modal">
                            Fechar
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="formEditar.processing">
                                {{ formEditar.processing ? 'Enviando...' : 'Salvar' }}
                            </button>
                        </div>
                     </form>
                </div>
            </div>
        </div>

        <!--MODAL DETALHE-->
        <div class="modal fade" id="modalDetalhes" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel3">Detallhes do Localização</h5>
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
                                <label for="emailLarge" class="form-label" >Contacto</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="contacto"></label>
                            </div>
                        </div>
                         <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label">Departamento</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="departamento"></label>
                            </div>
                        </div>
                         <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label">Cargo</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="cargo"></label>
                            </div>
                        </div>
                         <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label" >Data de Registo</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="data_registo"></label>
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



