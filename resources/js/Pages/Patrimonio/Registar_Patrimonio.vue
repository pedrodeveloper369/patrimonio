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
const filterStatus = ref('')
const filterDepartamento = ref('')
const filterResponsavel = ref('')
const filterLocal = ref('')
const filterCategoria = ref('')

//funcao que pesquisa os filtros, pega a lista de dados, merge com uma nova lista de modo a fazer funcionar os
// filtros e a nova lista é usada na tabela
const filteredUsers = computed(() => {
  return users.value.filter(u => {
    const matchesStatus = !filterStatus.value || u.estado === filterStatus.value
    const filterDepartamento = !filterDepartamento.value || u.estado === filterDepartamento.value
    const filterResponsavel = !filterResponsavel.value || u.estado === filterResponsavel.value
    const filterLocal = !filterLocal.value || u.estado === filterLocal.value
    const filterCategoria = !filterCategoria.value || u.role === filterCategoria.value
    return matchesStatus && filterDepartamento && filterResponsavel && filterLocal && filterCategoria
  })
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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Patrimonio/</span><strong>Registar Património</strong></h4>

        <div class="card p-4 " >
           <form @submit.prevent="submit">
    
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

                <button type="submit" class="btn btn-primary mt-3 float-right ml-3" :disabled="form.processing">
                    {{ form.processing ? 'Enviando...' : 'Salvar' }}
                </button>

                <button type="submit" class="btn btn-secondary mt-3 ml-6 float-right" :disabled="form.processing">
                    {{ form.processing ? 'Enviando...' : 'Salvar e Registar Novo' }}
                </button>
          
            </form>

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



