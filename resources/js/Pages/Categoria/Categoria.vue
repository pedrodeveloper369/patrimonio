<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, onMounted, computed, onUnmounted, watch  } from "vue";
import axios from 'axios';
import { useForm , usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2'

const categorias = ref([]);

const form = useForm({
    nome: ''
})

const formEditar = useForm({
    id: '',
    nome: '',
})

//declaracao do formulario e os seus dados
/*const form = useForm({
  nome: '',
  campos: [
    {
      nome: '',
      tipo: 'text'
    }
  ]
})*/

/*
const formEditar = useForm({
    id: '',
    nome: '',
    campos: [
        {
        nome: '',
        tipo: 'text'
        }
    ]
})*/

const formEliminar = useForm({
    ids: []
})

// Função para enviar
const submit = () => {
    form.post(route('categoria.registar'), {
        onSuccess: () => {
            $('#modalRegistar').modal('hide');
            resetModal()       // reseta o formulário
            listar_categorias()  //recarrega a tabela
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
  categorias.value = categorias.value.filter(u => !ids.includes(u.id));
}

function handleRowAction({ action, row }) {
  console.log(action, row);
}

//const categorias = ref(usePage().props.value.query);  caso os dados sao passados diretos na view
// Busca os dados do Laravel via rota relativa
onMounted(async () => {
  listar_categorias()
});

const listar_categorias = async () => {
  try {
    const response = await axios.get('/categoria/dados')
    categorias.value = response.data.data || response.data
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
    formEliminar.post(route('categoria.eliminar'), {
        onSuccess: () => {
            $('#modalEliminar').modal('hide');
            listar_categorias()  //recarrega a tabela
        }
    })
}

function openDeleteModal(ids) {
    deletingIds.value = ids;
    formEliminar.ids = ids;
    $('#modalEliminar').modal('show');
}


//ver detalhes
function ver_detalhes(categoria){

    document.getElementById('nome').innerText = categoria.name;
    document.getElementById('email').innerText = categoria.email;
    document.getElementById('contacto').innerText = categoria.contacto;
    document.getElementById('perfil').innerText = categoria.role;
    document.getElementById('estado').innerText = categoria.estado;
    document.getElementById('data_registo').innerText = categoria.created_at;

}

//editar categoria
function editar_categoria(categoria){
    formEditar.reset(); // limpa tudo corretamente
    formEditar.id = categoria.id;
    formEditar.nome= categoria.nome;
}

// Função para enviar
const submitEditar = () => {

    formEditar.post(route('categoria.editar'), {
        onSuccess: () => {
            $('#modalEditar').modal('hide');
            resetModal()       // reseta o formulário
            listar_categorias()  //recarrega a tabela
        }
    })
}

function adicionarCampo() {
  form.campos.push({
    nome: '',
    tipo: 'text'
  })
}

function removerCampo(index) {
  form.campos.splice(index, 1)
}


</script>

<template>
    <AuthenticatedLayout>
        <h4 class=""><strong>Categorias</strong></h4>

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
                <th>Data de Registo</th>
                <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="cat in categorias" :key="cat.id" :data-id="cat.id">
                <td></td>
                <td><strong style="color: #212529 !important;">{{ cat.nome }} </strong> <br></td>

                <td class="date-cell">{{ new Date(cat.created_at).toLocaleDateString() }}</td>
                <td>
                   <!-- <button class="" @click="ver_detalhes(cat)"   data-bs-toggle='modal' data-bs-target='#modalDetalhes' ><i class="menu-icon bx bx-show"></i></button>
                    -->
                    <button class=""  @click="editar_categoria(cat)"  data-bs-toggle='modal' data-bs-target='#modalEditar'><i class="menu-icon bx bx-edit-alt"></i></button>
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
                        <h5 class="modal-title" id="exampleModalLabel3">Registar Categoria</h5>
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
                                    <label for="nameLarge" class="">Nome da nova Categoria</label>
                                    <input type="text" v-model="form.nome" class="form-control" placeholder="Enter Name" />
                                    <div v-if="form.errors.nome" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.nome }}
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
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalRegistar" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel3">Registar Categoria</h5>
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

                            <label class="mb-2 fw-semibold">Campos especiais da Categoria</label>

                            <div
                                class="row mb-3 align-items-end"
                                v-for="(campo, index) in form.campos"
                                :key="index"
                            >

                                <!-- Nome do campo -->
                                <div class="col-md-5">
                                    <label>Nome</label>
                                    <input
                                        type="text"
                                        v-model="campo.nome"
                                        class="form-control"
                                        placeholder="Ex: Número de Série"
                                    />
                                </div>


                                <!-- Tipo do campo -->
                                <div class="col-md-5">
                                    <label>Tipo de Valor</label>
                                    <select v-model="campo.tipo" class="form-select">
                                        <option value="number">Numérico</option>
                                        <option value="char">Caracter</option>
                                        <option value="text">Texto</option>
                                        <option value="date">Data</option>
                                        <option value="datePast">Data Passada</option>
                                        <option value="dateFuture">Data Futura</option>
                                    </select>
                                </div>


                                <!-- Ações -->
                                <div class="col-md-2 text-end">
                                    <button
                                        v-if="index > 0"
                                        type="button"
                                        class="btn btn-sm"
                                        @click="removerCampo(index)"
                                    >
                                        <i class="bx bx-trash"></i> Eliminar
                                    </button>
                                </div>

                            </div>
                            <button

                                class="btn  btn-sm"
                                @click="adicionarCampo"
                                >
                                <i class="bx bx-plus"></i> Adicionar campo
                            </button>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary " data-bs-dismiss="modal">
                                Fechar
                                </button>
                                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                    {{ form.processing ? 'Enviando...' : 'Salvar' }}
                                </button>
                            </div>
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
                        <h5 class="modal-title" id="exampleModalLabel3">Actualizar Categoria</h5>
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

                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary " data-bs-dismiss="modal">
                                Fechar
                                </button>
                                <button type="submit" class="btn btn-primary" :disabled="formEditar.processing">
                                    {{ formEditar.processing ? 'Enviando...' : 'Salvar' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <!--
        <div class="modal fade" id="modalRegistar" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel3">Registar Categoria</h5>
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
                                    <label for="nameLarge" class="">Nome da nova Categoria</label>
                                    <input type="text" v-model="form.nome" class="form-control" placeholder="Enter Name" />
                                    <div v-if="form.errors.nome" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.nome }}
                                    </div>
                                </div>

                            </div>

                            <label class="mb-2 fw-semibold">Campos especiais da Categoria</label>

                            <div
                                class="row mb-3 align-items-end"
                                v-for="(campo, index) in form.campos"
                                :key="index"
                            >


                                <div class="col-md-5">
                                    <label>Nome</label>
                                    <input
                                        type="text"
                                        v-model="campo.nome"
                                        class="form-control"
                                        placeholder="Ex: Número de Série"
                                    />
                                </div>



                                <div class="col-md-5">
                                    <label>Tipo de Valor</label>
                                    <select v-model="campo.tipo" class="form-select">
                                        <option value="number">Numérico</option>
                                        <option value="char">Caracter</option>
                                        <option value="text">Texto</option>
                                        <option value="date">Data</option>
                                        <option value="datePast">Data Passada</option>
                                        <option value="dateFuture">Data Futura</option>
                                    </select>
                                </div>



                                <div class="col-md-2 text-end">
                                    <button
                                        v-if="index > 0"
                                        type="button"
                                        class="btn btn-sm"
                                        @click="removerCampo(index)"
                                    >
                                        <i class="bx bx-trash"></i> Eliminar
                                    </button>
                                </div>

                            </div>
                            <button

                                class="btn  btn-sm"
                                @click="adicionarCampo"
                                >
                                <i class="bx bx-plus"></i> Adicionar campo
                            </button>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary " data-bs-dismiss="modal">
                                Fechar
                                </button>
                                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                    {{ form.processing ? 'Enviando...' : 'Salvar' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    -->

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



