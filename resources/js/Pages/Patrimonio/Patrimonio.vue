<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, onMounted, computed, onUnmounted, watch  } from "vue";
import axios from 'axios';
import { useForm , usePage, router  } from '@inertiajs/vue3';
import Swal from 'sweetalert2'
import TabelaDinamica from '@/Components/TabelaDinamica.vue';

const patrimonio = ref([]);

//pegando dados vindo do controller
const props = defineProps({
    responsavel: Array,
    estado_patrimonio: Array,
    localizacao: Array,
    categoria: Array,
    departamento: Array,

});
const responsavel = ref(props.responsavel);
const estado_patrimonio = ref(props.estado_patrimonio);
const localizacao = ref(props.localizacao);
const categoria = ref(props.categoria);
const departamento = ref(props.departamento);
const patrimonioDocumento = ref(null);




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
    form.post(route('patrimonio.registar'), {
        onSuccess: () => {

            listar_patrimonios()  //recarrega a tabela
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


//filtros computed
const filterStatus = ref('')
const filterDepartamento = ref('')
const filterResponsavel = ref('')
const filterLocal = ref('')
const filterCategoria = ref('')
const filterStatusAquisicao = ref('')

//funcao que pesquisa os filtros, pega a lista de dados, merge com uma nova lista de modo a fazer funcionar os
// filtros e a nova lista é usada na tabela
const patrimonios = computed(() => {
  return patrimonio.value.filter(patri => {
    const matchesStatus = !filterStatus.value || patri.estado_patrimonio === filterStatus.value
    //const matchesStatusAq = !filterStatusAquisicao.value || patri.conservacao === filterStatusAquisicao.value
    //const filterDepartamento = !filterDepartamento.value || patri.estado === filterDepartamento.value
    const matchesResponsavel = !filterResponsavel.value || patri.responsavel === filterResponsavel.value
    //const matchesLocal = !filterLocal.value || patri.localizacao === filterLocal.value
    const matchesCategoria = !filterCategoria.value || patri.categoria === filterCategoria.value
    return matchesStatus /*&& matchesStatusAq*/ && matchesResponsavel /*&& matchesLocal*/ && matchesCategoria
  })
})

function handleDelete(ids) {
  patrimonio.value = patrimonio.value.filter(u => !ids.includes(patri.id));
}

function handleRowAction({ action, row }) {
  console.log(action, row);
}

//const patrimonio = ref(usePage().props.value.query);  caso os dados sao passados diretos na view
// Busca os dados do Laravel via rota relativa
onMounted(async () => {
  listar_patrimonios()
});

const listar_patrimonios = async () => {
  try {
    const response = await axios.get('/patrimonios/dados')
    patrimonio.value = response.data.data || response.data
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
    formEliminar.post(route('patrimonio.eliminar'), {
        onSuccess: () => {
            $('#modalEliminar').modal('hide');
            listar_patrimonios()  //recarrega a tabela
        }
    })
}

function openDeleteModal(ids) {
    deletingIds.value = ids;
    formEliminar.ids = ids;
    $('#modalEliminar').modal('show');
}

//ver detalhes
function ver_detalhes(patrimonio){
    document.getElementById('nome').innerText = patrimonio.nome;
    document.getElementById('codigo').innerText = patrimonio.codigo;
    document.getElementById('qtd').innerText = patrimonio.qtd;
    document.getElementById('custo').innerText = patrimonio.valor_compra;
    document.getElementById('origem').innerText = patrimonio.origem;
    document.getElementById('estado_aquisicao').innerText = patrimonio.conservacao;
    document.getElementById('marca').innerText = patrimonio.marca;
    document.getElementById('serie').innerText = patrimonio.num_serie;
    document.getElementById('categoria').innerText = patrimonio.categoria;
    document.getElementById('estado').innerText = patrimonio.estado_patrimonio;
    document.getElementById('responsavel').innerText = patrimonio.responsavel;
    document.getElementById('localizacao').innerText = patrimonio.localizacao +"\n( "+ patrimonio.caminhoLocal+" )";
    document.getElementById('descricao').innerText = patrimonio.descricao;
    document.getElementById('cor_patrimonio').innerText = patrimonio.cor_patrimonio;
    document.getElementById('data_registo').innerText = patrimonio.created_at;
}


function abrirModalFicheiro(documento) {
    this.patrimonioDocumento = documento;
}

//para rota
window.chamar_pagina_registar = () => {
  router.visit(route('registar.patrimonio'));
};

//para rota
window.chamar_pagina_registar_local = () => {
  router.visit(route('editar.patrimonio'));
};




</script>

<template>
    <AuthenticatedLayout>
        <h4 class=""><strong>Patrimónios</strong></h4>
         <div class="card  p-4 mb-2">
            <div class="d-flex flex-column flex-md-row gap-2 w-100">
                <div class="select-icon-wrapper equal-height">
                    <i class="bx bx-info-circle icon"></i>
                    <select v-model="filterStatus" class="form-select form-select-sm">
                        <option value="">Estado</option>
                         <option
                            v-for="estado in estado_patrimonio"
                            :key="estado.id"
                            :value="estado.nome"
                        >
                            {{ estado.nome }}
                        </option>
                    </select>
                </div>
                <!--<div class="select-icon-wrapper equal-height">
                    <i class="bx bx-info-circle icon"></i>
                    <select v-model="filterStatusAquisicao" class="form-select form-select-sm">
                        <option value="">Estado de Aquisição</option>
                         <option value="novo">Novo</option>
                         <option value="usado">Usado</option>
                         <option value="outro">Outro</option>

                    </select>
                </div>-->

              <!--  <div class="select-icon-wrapper equal-height">
                    <i class="bx bx-sitemap icon"></i>
                    <select v-model="filterDepartamento" class="form-select form-select-sm">
                        <option value="">Departamento</option>
                        <option
                            v-for="depa in departamento"
                            :key="depa.id"
                            :value="depa.nome"
                        >
                            {{ depa.nome }}
                        </option>
                    </select>
                </div>-->

                <div class="select-icon-wrapper equal-height">
                    <i class="bx bx-user icon"></i>
                    <select v-model="filterResponsavel" class="form-select form-select-sm">
                        <option value="">Responsável</option>
                         <option
                            v-for="respo in responsavel"
                            :key="respo.id"
                            :value="respo.nome"
                        >
                            {{ respo.nome }}
                        </option>
                    </select>
                </div>

                <!--<div class="select-icon-wrapper equal-height">
                    <i class="bx bx-map icon"></i>
                    <select v-model="filterLocal" class="form-select form-select-sm">
                        <option value="">Localização</option>
                        <option
                            v-for="local in localizacao"
                            :key="local.id"
                            :value="local.nome"
                        >
                            {{ local.nome }}
                        </option>
                    </select>
                </div>-->

                 <div class="select-icon-wrapper equal-height">
                    <i class="bx bx-category icon"></i>
                    <select v-model="filterCategoria" class="form-select form-select-sm">
                        <option value="">Categoria</option>
                         <option
                            v-for="cate in categoria"
                            :key="cate.id"
                            :value="cate.nome"
                        >
                            {{ cate.nome }}
                        </option>
                    </select>
                </div>

            </div>

        </div>

        <div class="card p-4 " >
            <!--<button class='btn btn-outline-danger btn-sm' id='btn-add'><i class='menu-icon bx bx-export'></i> PDF</button>
                           -->

            <div class="table-responsive text-nowrap mt-3">
                <table v-datatable="{datatableOptions, defaultPageSize: 10,
                        deleteAction: (selectedIds) => {
                            //chama modal
                            openDeleteModal(selectedIds);
                        },
                        actionsHtml: `

                            <button onclick='window.chamar_pagina_registar()'  class='btn btn-primary btn-sm' id='btn-add'><i class='menu-icon bx bx-plus'></i> Adicionar</button>

                        `
                        }"
                    @selection-changed="onSelectionChanged"
                    @datatable-delete="onDeleteRequested"
                     @datatable-action="onDatatableAction"
                    class="table table-hover table-striped mt-3 min-w-full  mt-6 text-sm"
                >

                <thead class="bg-gray-100 ">
                    <tr>
                    <th></th>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Responsável</th>
                    <th>Estado</th>
                    <th>Data de Registo</th>
                    <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="patri in patrimonios" :key="patri.id" :data-id="patri.id">
                    <td></td>
                    <td>
                        <img
                            v-if="patri.imagem"
                            :src="`/storage/patrimonios/imagens/${patri.imagem}`"
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

                    <td><strong style="color: #212529 !important;">{{ patri.nome }} </strong> <br></td>
                    <td>{{ patri.categoria }}</td>
                    <td>{{ patri.responsavel }}</td>
                    <td >
                        <span
                            class="px-2 py-1 text-xs font-semibold rounded"
                             :style="{ backgroundColor: patri.background, color: patri.cor }"

                        >
                            {{ patri.estado_patrimonio }}
                        </span>

                    </td>

                    <td class="date-cell">{{ new Date(patri.created_at).toLocaleDateString() }}</td>

                    <td>
                        <button class="" @click="ver_detalhes(patri)"   data-bs-toggle='modal' data-bs-target='#modalDetalhes' ><i class="menu-icon bx bx-show"></i></button>
                        <Link :href="route('editar.patrimonio', patri)" style="color:#777"><i class="menu-icon bx bx-edit-alt"></i></Link>

                        <Link :href="route('movimento.patrimonio', patri)" style="color:#777"> <i class="bx bx-transfer me-2"></i> </Link>
                        <Link :href="route('movimento.historico', patri.id)" style="color:#777"> <i class="bx bx-list-ul"></i></Link>

                        <button
                            v-if="patri.documento"
                            class=""
                             @click="abrirModalFicheiro(patri.documento)"
                            data-bs-toggle="modal"
                            data-bs-target="#modalficheiro"
                        >
                            <i class="bx bx-file me-2"></i>
                        </button>

                    </td>

                    </tr>
                </tbody>
                </table>
            </div>

        </div>

        <!--MODAL ELIMINAR-->
        <div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true" >
            <div class="modal-dialog modal-sm" role="document" >
                <div class="modal-content" style="border:1px solid #debbb3">
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

        <!--MODAL DETALHE-->
        <div class="modal fade" id="modalDetalhes" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="border:1px solid #debbb3">
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
                                <label for="emailLarge" class="form-label">Código</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="codigo"></label>
                            </div>
                        </div>
                          <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label">Série</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="serie"></label>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label">Cor</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="cor_patrimonio"></label>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label">Marca</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="marca"></label>
                            </div>
                        </div>
                          <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label">Quantidade</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="qtd"></label>
                            </div>
                        </div>
                         <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label">Custo</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="custo"></label>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label">Origem</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="origem"></label>
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
                                <label for="emailLarge" class="form-label" >Responsavel</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="responsavel"></label>
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
                                <label for="emailLarge" class="form-label">Estado de Aquisição</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="estado_aquisicao"></label>
                            </div>
                        </div>
                         <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label">Localização</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="localizacao"></label>
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

                         <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailLarge" class="form-label">Descrição</label>
                            </div>
                            <div class="col mb-0">
                                <label for="dobLarge" class="form-label" id="descricao"></label>
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

         <!--MODAL FICHEIRO-->
        <div class="modal fade" id="modalficheiro" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" style="border:1px solid #debbb3">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel3">Documento</h5>
                        <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">

                        <iframe
                            :src="`/storage/patrimonios/documentos/${patrimonioDocumento}`"
                            width="100%"
                            height="700px"
                            frameborder="0"
                        ></iframe>
                        <p class="mt-2">
                            <a :href="`/storage/${patrimonioDocumento}`" target="_blank">
                                Abrir em nova aba
                            </a>
                        </p>



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



