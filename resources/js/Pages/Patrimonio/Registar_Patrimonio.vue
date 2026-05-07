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
});

const estadoPatrimonio = ref(props.estadoPatrimonio);
const categorias = ref(props.categorias);
const caminhosLocal = ref(props.caminhosLocal);
const responsaveis = ref(props.responsaveis);
const localSelecionado = ref(null)

//declaracao do formulario e os seus dados
const form = useForm({
    nome: '',
    codigo: '',
    descricao: '',
    qtd: 1,
    imagem: null,
    valor_compra: '',
    origem: '',
    conservacao: '',
    documento: null,
    id_categoria: '',
    id_local: 1,
    local: '',
    id_estado_patrimonio: '',
    marca: '',
    cor: '',
    num_serie: '',
    responsavel: '',
})

const previewImagem = ref(null)

function handleFile(event) {

    const file = event.target.files[0]

    if (!file) return

    form.imagem = file

    if (previewImagem.value) {
        URL.revokeObjectURL(previewImagem.value)
    }

    previewImagem.value = URL.createObjectURL(file)
}


function handleFileDoc(event) {
   form.documento = event.target.files[0]
}

// Função para enviar
const submit = () => {
    form.post(route('patrimonio.registar'), {
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
    router.visit(route('patrimonio'));
}


</script>

<template>
    <AuthenticatedLayout>
        <h4 class="fw-bold py-3 mb-4"><Link :href="route('patrimonio')"  class="text-muted fw-light">Patrimonio/</Link><strong>Registar Património</strong></h4>

        <div class="card p-4 ">

           <form @submit.prevent="submit" class="mt-3">


                <div class="row g-2 mb-3">
                    <div class="col mb-0">
                        <label for="emailLarge" class="">Nome do Património *</label>
                        <input type="text" v-model="form.nome" class="form-control" />
                        <div v-if="form.errors.nome" class="text-red-500 text-sm mt-1">
                            {{ form.errors.nome }}
                        </div>
                    </div>
                    <div class="col mb-0">
                        <label for="dobLarge" class="">Código *</label>
                        <input type="text" v-model="form.codigo" class="form-control"  />
                        <div v-if="form.errors.codigo" class="text-red-500 text-sm mt-1">
                            {{ form.errors.codigo }}
                        </div>
                    </div>

                    <div class="col mb-0">
                        <label for="emailLarge" class="">Quantidade (Opcional)</label>
                        <input type="number" min="1" v-model="form.qtd" class="form-control"  />
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col mb-0">
                        <label for="dobLarge" class="">Custo (Opcional)</label>
                        <input type="number" v-model="form.valor_compra" class="form-control"  />
                    </div>

                    <div class="col mb-0">
                        <label for="emailLarge" class="">Origem (Opcional)</label>
                        <input type="text" v-model="form.origem" class="form-control" />
                    </div>
                    <div class="col mb-0">
                        <label for="dobLarge" class="">Conservação *</label>
                        <select v-model="form.conservacao" class="form-select">
                            <option value="">Seleccione o estado de aquisição</option>
                            <option value="Novo" >Novo</option>
                            <option value="Usado" >Usado</option>
                            <option value="Outro" >Outro</option>
                        </select>
                        <div v-if="form.errors.conservacao" class="text-red-500 text-sm mt-1">
                            {{ form.errors.conservacao }}
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col mb-0">
                        <label for="emailLarge" class="">Categoria *</label>
                        <select v-model="form.id_categoria" class="form-select">
                            <option value="">Seleccione a categoria</option>

                            <option
                                v-for="cat in categorias"
                                :key="cat.id"
                                :value="cat.id"
                            >
                                {{ cat.nome }}
                            </option>

                        </select>

                        <div v-if="form.errors.id_categoria" class="text-red-500 text-sm mt-1">
                            {{ form.errors.id_categoria }}
                        </div>
                    </div>
                    <div class="col mb-0">
                        <label for="dobLarge" class="">Localização</label>

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
                        <label for="dobLarge" class="">Estado do Património *</label>
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

                        <div v-if="form.errors.id_estado_patrimonio" class="text-red-500 text-sm mt-1">
                            {{ form.errors.id_estado_patrimonio }}
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col mb-0">
                        <label for="emailLarge" class="">Marca (Opcional)</label>
                        <input type="text" v-model="form.marca" class="form-control" />
                    </div>
                    <div class="col mb-0">
                        <label for="dobLarge" class="">Série (Opcional)</label>
                        <input type="text" v-model="form.num_serie" class="form-control" />
                    </div>
                    <div class="col mb-0">
                        <label for="dobLarge" class="">Cor (Opcional)</label>
                        <input type="text" v-model="form.cor" class="form-control" />
                    </div>
                </div>

                <!--imagem pre visualizada ao carregar uma imagem para o patrimonio-->
                <img v-if="previewImagem" :src="previewImagem" class="img-thumbnail mt-2" style="width:100px; border-radius:9px" />

                <div class="row g-2 mb-3">

                    <div class="col mb-0">
                        <label for="emailLarge" class="">Imagem (Opcional)</label>
                        <input
                            type="file"
                            class="form-control"
                            accept="image/*"
                            @change="handleFile"
                        />
                        <div v-if="form.errors.imagem" class="text-red-500 text-sm mt-1">
                            {{ form.errors.imagem }}
                        </div>
                    </div>
                    <div class="col mb-0">
                        <label for="dobLarge" class="">Documento (Opcional)</label>
                        <input
                            type="file"
                            class="form-control"
                            @change="handleFileDoc"
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.jpeg,.PNG"
                        />

                        <div v-if="form.errors.documento" class="text-red-500 text-sm mt-1">
                            {{ form.errors.documento }}
                        </div>
                    </div>
                </div>
                 <div class="col mb-3">
                        <label for="dobLarge" class="">Responsável (Opcional)</label>
                       <select v-model="form.responsavel" class="form-select">
                            <option value="">Seleccione o responsável do património</option>

                            <option
                                v-for="resp in responsaveis"
                                :key="resp.id"
                                :value="resp.id"
                            >
                               {{ resp.nome }} / Dep: {{ resp.departamento }}
                            </option>

                        </select>
                    </div>
                <div class="row g-2 mb-3">
                    <div class="col mb-0">
                        <label for="dobLarge" class="">Descrição (Opcional)</label>
                        <textarea type="text" v-model="form.descricao" class="form-control" />
                        <div v-if="form.errors.descricao" class="text-red-500 text-sm mt-1">
                            {{ form.errors.descricao }}
                        </div>
                    </div>
                </div>


                <div class="mt-4 float-right ">

                    <button
                        type="submit"
                        class="btn btn-primary ms-auto"
                        :disabled="form.processing"
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



