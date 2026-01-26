<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, onMounted, computed, onUnmounted, watch  } from "vue";
import axios from 'axios';
import { useForm , usePage , router} from '@inertiajs/vue3';
import Swal from 'sweetalert2'
import LocalTree from '@/Components/LocalTree.vue'

const users = ref([]);
const passo = ref(1)

const props = defineProps({
    estadoPatrimonio: Array,
    categorias: Array,
    caminhosLocal: Array,
    responsaveis: Array,
    patrimonio: Object,
});

const estadoPatrimonio = ref(props.estadoPatrimonio);
const categorias = ref(props.categorias);
const caminhosLocal = ref(props.caminhosLocal);
const responsaveis = ref(props.responsaveis);
const patrimonio = ref(props.patrimonio);
const localSelecionado = ref(null)

function proximo() {
  if (passo.value < 2) passo.value++
}

function anterior() {
  if (passo.value > 1) passo.value--
}

//declaracao do formulario e os seus dados
const form = useForm({
    id: patrimonio.value.id,
    local: '',
    id_local: '',
    id_local_antigo: patrimonio.value.id_localizacao,
    id_estado_patrimonio: '',
    id_estado_antigo: patrimonio.value.id_estado_patrimonio,
    responsavel: '',
    responsavel_antigo: patrimonio.value.id_responsavel,
    descricao: '',
})


const podeSalvar = computed(() => {
    return (
        !!form.local ||
        !!form.id_estado_patrimonio ||
        !!form.responsavel
    )
})
/*
local_actual = patrimonio.localizacao ;
estado_actual = patrimonio.estado_patrimonio;
responsa_actual = patrimonio.responsavel;
*/
// Função para enviar
const submit = () => {
    form.post(route('movimentar.patrimonio'), {
        onSuccess: () => {
            redirecionar_pagina();
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


//funcao de selecção exclusiva
function selecionarLocal(local) {
    localSelecionado.value = local
    showWarning.value = false // esconde a mensagem se o usuário selecionou algo
}

function abrirmodal(){
    localSelecionado.value = '';
    $('#modalBuscarLocal').modal('show')
}

function confirmarLocal() {
    if (!localSelecionado.value) {
        showWarning.value = true
        return
    }

    form.id_local = localSelecionado.value.id
    form.local = localSelecionado.value.caminho

    $('#modalBuscarLocal').modal('hide')
}

function redirecionar_pagina(){
    router.visit(route('movimentacao'));
}


</script>

<template>
    <AuthenticatedLayout>
        <h4 class="fw-bold py-3 mb-4"><Link :href="route('movimentacao')" class="text-muted fw-light">Movimentações/</Link><strong>Movimentar Património</strong></h4>

        <div class="card p-4 ">

                <div class="row g-2">
                    <div class="col mb-2">
                        <label for="emailLarge" class=""><strong>Património:</strong>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; {{patrimonio.nome_patrimonio}}</label>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col mb-2">
                        <label for="emailLarge" class=""><strong>Responsável Actual:</strong>&nbsp;&nbsp;&nbsp; {{patrimonio.responsavel}}</label>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col mb-2">
                        <label for="emailLarge" class=""><strong>Estado Actual:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{patrimonio.estado_patrimonio}}</label>
                    </div>
                </div>
                <div class="row g-2 ">
                    <div class="col mb-4">
                        <label for="emailLarge" class=""><strong>Localização Actual:</strong> &nbsp;&nbsp;&nbsp;&nbsp; {{patrimonio.localizacao}} ({{patrimonio.caminhoLocal}})</label>
                    </div>
                </div>
                 <img
                    v-if="patrimonio.imagem"
                    :src="`/storage/patrimonios/imagens/${patrimonio.imagem}`"
                    alt="Imagem do Património"
                    style="width:200px; height:auto; border-radius:9px"
                >
                <img
                    v-else
                    src="/assets/img/avatars/pitruca.webp"
                    alt="Imagem padrão"
                    style="width:50px; height:auto; border-radius:9px"
                >

           <form @submit.prevent="submit" class="mt-6">

                <div class="row g-2 mb-3">

                    <div class="col mb-0">
                        <input type="text" v-model="form.id" hidden>
                        <input type="text" v-model="form.id_local_antigo" hidden>
                        <input type="text" v-model="form.id_estado_antigo" hidden>
                        <input type="text" v-model="form.responsavel_antigo" hidden>

                        <label for="dobLarge" class="">Nova Localização</label>

                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control"
                                v-model="form.local"
                                readonly
                                placeholder="Seleccione a localização"
                            />

                            <button style="width: 110px"
                                class="btn btn-outline-secondary"
                                type="button"

                                @click="abrirmodal()"
                            >
                                <i class="bx bx-search"></i>
                                Buscar
                            </button>
                        </div>
                        <small>Se nenhuma localização for seleccionada, o bem passa para a local raiz</small>
                    </div>
                    <div class="col mb-0">
                        <label for="dobLarge" class="">Novo Estado do Património</label>
                       <select v-model="form.id_estado_patrimonio" class="form-select">
                            <option value="">Seleccione o estado do património</option>

                            <option
                                v-for="estado in estadoPatrimonio"
                                :key="estado.id"
                                :value="estado.id"
                            >
                                {{ estado.nome }}
                            </option>

                        </select>


                    </div>
                     <div class="col mb-3">
                    <label for="dobLarge" class="">Novo Responsável (Opcional)</label>
                    <select v-model="form.responsavel" class="form-select">
                        <option value="">Seleccione o novo responsável</option>
                        <option
                            v-for="resp in responsaveis"
                            :key="resp.id"
                            :value="resp.id"
                        >
                            {{ resp.nome }} / Dep: {{ resp.departamento }}
                        </option>

                    </select>
                </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col mb-0">
                        <label for="dobLarge" class="">Descrição (Opcional)</label>
                        <textarea type="text" v-model="form.descricao" class="form-control" />

                    </div>
                </div>

                <div class="mt-4 float-right ">

                    <button
                        type="submit"
                        class="btn btn-primary ms-auto"
                        :disabled="form.processing || !podeSalvar"
                    >
                        {{ form.processing ? 'Salvando...' : 'Salvar' }}
                    </button>
                </div>
            </form>
        </div>




         <!--Modal para selecionar a localização-->
        <div class="modal fade" id="modalBuscarLocal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Seleccionar Localização</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="text-orange-500 text-sm" v-if="showWarning">
                        Deverás seleccionar um local antes de confirmar
                    </label>

                    <LocalTree
                        v-for="local in caminhosLocal"
                        :key="local.id"
                        :local="local"
                        :selectedId="localSelecionado?.id"
                        @select="selecionarLocal"
                    />

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button class="btn btn-primary"  @click="confirmarLocal">
                        Confirmar
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



