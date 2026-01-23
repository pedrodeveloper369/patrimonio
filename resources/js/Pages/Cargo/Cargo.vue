<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, onMounted, computed, onUnmounted, watch  } from "vue";
import axios from 'axios';
import { useForm , usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2'

/*
//pegando a lista de cargos vindo da view
const props = defineProps({
  cargo: Array,
});
const cargos = ref(props.cargo);
*/

const cargos = ref([]);


//declaracao do formulario e os seus dados
const form = useForm({
    nome: '',
})

const formEditar = useForm({
    id: '',
    nome: '',
})

const formEliminar = useForm({
    ids: []
})


//atualiza a lista de cargos, ao acessar a rota
onMounted(async () => {
  listar_cargos()
});
//funcao que pega os a lista de cargos
const listar_cargos = async () => {
  try {
    const response = await axios.get('/cargo/dados')
    cargos.value = response.data.data || response.data
  } catch (error) {
    console.error('Erro ao carregar cargos:', error)
  }
}


// Função para enviar
const submit = () => {
    form.post(route('cargo.registar'), {
        onSuccess: () => {
            $('#modalRegistar').modal('hide');
            resetModal()
            listar_cargos()  //recarrega a tabela

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


function handleDelete(ids) {
  users.value = users.value.filter(u => !ids.includes(u.id));
}

function handleRowAction({ action, row }) {
  console.log(action, row);
}

//eliminar registo da tabela
const deletingIds = ref([]);
const selectedToDelete = ref([]);      // IDs
const deleteMessage = ref("");         // Mensagem que a modal vai mostrar
const showDeleteModal = ref(false);    // Controla a modal

const submitEliminar = () => {
    formEliminar.post(route('cargo.eliminar'), {
        onSuccess: () => {
            $('#modalEliminar').modal('hide');
            listar_cargos()  //recarrega a tabela
        }
    })
}


function openDeleteModal(ids) {
    deletingIds.value = ids;
    formEliminar.ids = ids;
    $('#modalEliminar').modal('show');
}

//editar cargoutilizador
function editar_cargo(cargo){
    formEditar.reset(); // limpa tudo corretamente
    formEditar.id = cargo.id;
    formEditar.nome = cargo.nome;
}

// Função para enviar
const submitEditar = () => {
    formEditar.post(route('cargo.editar'), {
        onSuccess: () => {
            $('#modalEditar').modal('hide');
            resetModal()       // reseta o formulário
            listar_cargos()  //recarrega a tabela
        }
    })
}

</script>

<template>
    <AuthenticatedLayout>
        <h4 class=""><strong>Cargos</strong></h4>

        <div class="card p-4 ">

            <div class="table-responsive text-nowrap mt-3">
            <table v-datatable="{datatableOptions, defaultPageSize: 10,
                    deleteAction: (selectedIds) => {
                        //chama modal
                        openDeleteModal(selectedIds);
                    },
                    actionsHtml: `
                        <button class='btn btn-primary btn-sm' id='btn-add'  data-bs-toggle='modal' data-bs-target='#modalRegistar'><i class='menu-icon bx bx-plus'></i> Adicionar</button>
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
                <th>Data de Criação</th>
                <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="dep in cargos" :key="dep.id" :data-id="dep.id">
                <td></td>
                <td class="p-3"><strong style="color: #212529 !important;">{{ dep.nome }} </strong> <br> </td>



                <td class="date-cell">{{ new Date(dep.created_at).toLocaleDateString() }}</td>
                <td>
                    <button class=""  @click="editar_cargo(dep)"  data-bs-toggle='modal' data-bs-target='#modalEditar'><i class="menu-icon bx bx-edit-alt"></i></button>
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
                        <h5 class="modal-title" id="exampleModalLabel3">Registar Cargo</h5>
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
                        <h5 class="modal-title" id="exampleModalLabel3">Actualizar Cargo</h5>
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



