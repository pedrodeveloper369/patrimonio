<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, onMounted, computed, onUnmounted, watch  } from "vue";
import axios from 'axios';
import { useForm , usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2'

const users = ref([]);

//declaracao do formulario e os seus dados
const form = useForm({
    nome: '',
    contacto: '',
    email: '',
    senha: '',
    confirma_senha: '',
})

const formEditar = useForm({
    id: '',
    nome_editar: '',
    contacto_editar: '',
    email_editar: '',
    email_copia_editar: '',
    estado: '',
    senha_editar: '',
    confirma_senha_editar: '',
    estado: '',
})

const formEliminar = useForm({
    ids: []
})

// Função para enviar
const submit = () => {
    form.post(route('utilizador.registar'), {
        onSuccess: () => {
            $('#modalRegistar').modal('hide');
            resetModal()       // reseta o formulário
            listar_utilizadores()  //recarrega a tabela
        }
    })
}





// Função para resetar o formulário
function resetModal() {
    form.reset()
    form.clearErrors()
}

onMounted(() => {
    const modalEl = document.getElementById('modalRegistar')
    modalEl.addEventListener('hidden.bs.modal', resetModal)
})


const columns = [
  { label: 'Nome', key: 'name' },
  { label: 'Contacto', key: 'contacto' },
  { label: 'Perfil', key: 'role' },
  { label: 'Status', key: 'status' },
  { label: 'Criado em', key: 'created_at' },
];

function handleDelete(ids) {
  users.value = users.value.filter(u => !ids.includes(u.id));
}

function handleRowAction({ action, row }) {
  console.log(action, row);
}

//const users = ref(usePage().props.value.query);  caso os dados sao passados diretos na view
// Busca os dados do Laravel via rota relativa
onMounted(async () => {
  listar_utilizadores()
});

const listar_utilizadores = async () => {
  try {
    const response = await axios.get('/utilizadores/dados')
    users.value = response.data.data || response.data
  } catch (error) {
    console.error('Erro ao carregar usuários:', error)
  }
}

//eliminar registo da tabela
const deletingIds = ref([]);
const selectedToDelete = ref([]);      // IDs
const deleteMessage = ref("");         // Mensagem que a modal vai mostrar
const showDeleteModal = ref(false);    // Controla a modal

const submitEliminar = () => {
    formEliminar.post(route('utilizador.eliminar'), {
        onSuccess: () => {
            $('#modalEliminar').modal('hide');
            listar_utilizadores()  //recarrega a tabela
        }
    })
}


function openDeleteModal(ids) {
    deletingIds.value = ids;
    formEliminar.ids = ids;
    $('#modalEliminar').modal('show');
}


//ver detalhes
function ver_detalhes(utilizador){

    document.getElementById('nome').innerText = utilizador.name;
    document.getElementById('email').innerText = utilizador.email;
    document.getElementById('contacto').innerText = utilizador.contacto;
    document.getElementById('perfil').innerText = utilizador.role;
    document.getElementById('estado').innerText = utilizador.estado;
    document.getElementById('data_registo').innerText = utilizador.created_at;

}

//editar utilizador
function editar_utilizador(utilizador){
    formEditar.reset(); // limpa tudo corretamente

    formEditar.id = utilizador.id;
    formEditar.nome_editar = utilizador.name;
    formEditar.estado = utilizador.estado;
    formEditar.contacto_editar = utilizador.contacto;
    formEditar.email_editar = utilizador.email;
    formEditar.email_copia_editar = utilizador.email;
    formEditar.senha_editar =  utilizador.password;   // opcional
    formEditar.confirma_senha_editar =  utilizador.password; // opcional
}

// Função para enviar
const submitEditar = () => {
    formEditar.post(route('utilizador.editar'), {
        onSuccess: () => {
            $('#modalEditar').modal('hide');
            resetModal()       // reseta o formulário
            listar_utilizadores()  //recarrega a tabela
        }
    })
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
                          data-bs-target="#navs-justified-home"
                          aria-controls="navs-justified-home"
                          aria-selected="true"
                        >
                          <i class="tf-icons bx bx-home"></i> Movimentações
                        </button>
                      </li>
                      <li class="nav-item">
                        <button
                          type="button"
                          class="nav-link"
                          role="tab"
                          data-bs-toggle="tab"
                          data-bs-target="#navs-justified-profile"
                          aria-controls="navs-justified-profile"
                          aria-selected="false"
                        >
                          <i class="tf-icons bx bx-user"></i> Patrimónios Movimentados
                        </button>
                      </li>
                    </ul>
                    <div class="tab-content">
                      <div class="tab-pane fade show active" id="navs-justified-home" role="tabpanel">
                            <div class="table-responsive text-nowrap mt-3">
                                <table v-datatable="{datatableOptions, defaultPageSize: 10,
                                        deleteAction: (selectedIds) => {
                                            //chama modal
                                            openDeleteModal(selectedIds);
                                        },
                                        actionsHtml: `

                                        `
                                        }"
                                    @selection-changed="onSelectionChanged"
                                    @datatable-delete="onDeleteRequested"
                                    class="table table-hover mt-3 min-w-full  mt-6 text-sm"
                                >

                                <thead class="bg-gray-100 ">
                                    <tr>
                                    <th hidden></th>
                                    <th v-for="col in columns" :key="col.key">{{ col.label }}</th>
                                    <th>Ações</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="u in users" :key="u.id" :data-id="u.id">
                                    <td hidden></td>
                                    <td><strong style="color: #212529 !important;">{{ u.name }} </strong> <br> {{ u.email }}</td>
                                    <td>{{ u.contacto }}</td>
                                    <td>{{ u.role }}</td>
                                    <td>
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded"
                                            :class="{
                                            'bg-green-100 text-green-700': u.estado === 'Activo',
                                            'bg-red-100 text-red-700': u.estado === 'Inactivo',
                                            //'bg-yellow-100 text-yellow-700': u.estado === 'Pending',
                                            }"
                                        >
                                            {{ u.estado }}
                                        </span>

                                    </td>

                                    <td class="date-cell">{{ new Date(u.created_at).toLocaleDateString() }}</td>
                                    <td>
                                        <button class="" @click="ver_detalhes(u)"   data-bs-toggle='modal' data-bs-target='#modalDetalhes' ><i class="menu-icon bx bx-show"></i></button>
                                    </td>
                                    </tr>
                                </tbody>
                                </table>
                            </div>
                      </div>
                      <div class="tab-pane fade" id="navs-justified-profile" role="tabpanel">
                        <label for="">Patrimonios</label>
                            <div class="table-responsive text-nowrap mt-3">
                                <table v-datatable="{datatableOptions, defaultPageSize: 10,
                                        deleteAction: (selectedIds) => {
                                            //chama modal
                                            openDeleteModal(selectedIds);
                                        },
                                        actionsHtml: `

                                        `
                                        }"
                                    @selection-changed="onSelectionChanged"
                                    @datatable-delete="onDeleteRequested"
                                    class="table table-hover mt-3 min-w-full  mt-6 text-sm"
                                >

                                <thead class="bg-gray-100 ">
                                    <tr>
                                    <th hidden></th>
                                    <th v-for="col in columns" :key="col.key">{{ col.label }}</th>
                                    <th>Ações</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="u in users" :key="u.id" :data-id="u.id">
                                    <td hidden></td>
                                    <td><strong style="color: #212529 !important;">{{ u.name }} </strong> <br> {{ u.email }}</td>
                                    <td>{{ u.contacto }}</td>
                                    <td>{{ u.role }}</td>
                                    <td>
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded"
                                            :class="{
                                            'bg-green-100 text-green-700': u.estado === 'Activo',
                                            'bg-red-100 text-red-700': u.estado === 'Inactivo',
                                            //'bg-yellow-100 text-yellow-700': u.estado === 'Pending',
                                            }"
                                        >
                                            {{ u.estado }}
                                        </span>

                                    </td>

                                    <td class="date-cell">{{ new Date(u.created_at).toLocaleDateString() }}</td>
                                    <td>
                                        <button class="" @click="ver_detalhes(u)"   data-bs-toggle='modal' data-bs-target='#modalDetalhes' ><i class="menu-icon bx bx-show"></i></button>
                                    </td>
                                    </tr>
                                </tbody>
                                </table>
                            </div>
                      </div>
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
                        <h5 class="modal-title" id="exampleModalLabel3">Registar Utilizador</h5>
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
                                <div class="row  mb-3">
                                    <div class="col mb-0">
                                        <label for="emailLarge" class="">Contacto</label>
                                        <input type="text" v-model="form.contacto" class="form-control" placeholder="xxxx@xxx.xx" />
                                        <div v-if="form.errors.contacto" class="text-red-500 text-sm mt-1">
                                            {{ form.errors.contacto }}
                                        </div>

                                    </div>

                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col mb-0">
                                        <label for="emailLarge" class="">Email</label>
                                        <input type="text" v-model="form.email" class="form-control" placeholder="xxxx@xxx.xx" />
                                    <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.email }}
                                    </div>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col mb-0">
                                        <label for="emailLarge" class="">Palavra Passe</label>
                                        <input type="password" v-model="form.senha" class="form-control" placeholder="******" />
                                        <div v-if="form.errors.senha" class="text-red-500 text-sm mt-1">
                                            {{ form.errors.senha }}
                                        </div>
                                    </div>
                                    <div class="col mb-0">
                                        <label for="dobLarge" class="">Confirmar Palavra Passe</label>
                                        <input type="password" v-model="form.confirma_senha" class="form-control" placeholder="******" />
                                        <div v-if="form.errors.confirma_senha" class="text-red-500 text-sm mt-1">
                                            {{ form.errors.confirma_senha }}
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
                        <h5 class="modal-title" id="exampleModalLabel3">Actualizar Utilizador</h5>
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
                                        <input id="nome_edit"  type="text" v-model="formEditar.nome_editar" class="form-control" placeholder="Enter Name" />
                                        <div v-if="formEditar.errors.nome_editar" class="text-red-500 text-sm mt-1">
                                            {{ formEditar.errors.nome_editar }}
                                        </div>
                                    </div>

                                </div>
                                <div class="row  mb-3">
                                    <div class="col mb-0">
                                        <label for="emailLarge" class="">Contacto</label>
                                        <input id="contacto_edit" type="text" v-model="formEditar.contacto_editar" class="form-control" placeholder="xxxx@xxx.xx" />
                                        <div v-if="formEditar.errors.contacto_editar" class="text-red-500 text-sm mt-1">
                                            {{ formEditar.errors.contacto_editar }}
                                        </div>

                                    </div>

                                </div>
                                <div class="row  mb-3">
                                    <div class="col mb-0">
                                        <label for="emailLarge" class="">Estado</label>
                                       <select v-model="formEditar.estado" class="form-select">
                                            <option value="Activo">Activar</option>
                                            <option value="Inactivo">Desactivar</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col mb-0">
                                        <label for="emailLarge" class="">Email</label>
                                        <input id="email_edit" type="text" v-model="formEditar.email_editar" class="form-control" placeholder="xxxx@xxx.xx" />
                                            <input id="email_copia_edit" type="hidden" v-model="formEditar.email_copia_editar" class="form-control" placeholder="xxxx@xxx.xx" />

                                    <div v-if="formEditar.errors.email_editar" class="text-red-500 text-sm mt-1">
                                        {{ formEditar.errors.email_editar }}
                                    </div>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col mb-0">
                                        <label for="emailLarge" class="">Palavra Passe</label>
                                        <input id="senha_edit" type="password" v-model="formEditar.senha_editar" class="form-control" placeholder="******" />
                                        <div v-if="formEditar.errors.senha_editar" class="text-red-500 text-sm mt-1">
                                            {{ formEditar.errors.senha_editar }}
                                        </div>
                                    </div>
                                    <div class="col mb-0">
                                        <label for="dobLarge" class="">Confirmar Palavra Passe</label>
                                        <input id="confirmar_senha_edit" type="password" v-model="formEditar.confirma_senha_editar" class="form-control" placeholder="******" />
                                        <div v-if="formEditar.errors.confirma_senha_editar" class="text-red-500 text-sm mt-1">
                                            {{ formEditar.errors.confirma_senha_editar }}
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
                        <h5 class="modal-title" id="exampleModalLabel3">Detallhes do Utilizador</h5>
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
                                <label for="emailLarge" class="form-label" >Email</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="email"></label>
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
                                <label for="emailLarge" class="form-label">Perfil</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="perfil"></label>
                            </div>
                        </div>
                         <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label">Estado</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="estado"></label>
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



