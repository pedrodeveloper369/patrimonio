<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, onMounted, computed, onUnmounted, watch  } from "vue";
import axios from 'axios';
import { useForm , usePage, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2'
import LocalTree from '@/Components/LocalTree.vue'

const users = ref([]);
const props = defineProps({
  tipoLocal: Array,
  caminhosLocal: Array,
});
const tipoLocal = ref(props.tipoLocal);
const caminhosLocal = ref(props.caminhosLocal);
const localSelecionado = ref(null)
const showWarning = ref(false)


//declaracao do formulario e os seus dados
const form = useForm({
    nome: '',
    tipoLocalizacao: '',
    local: '',
    id_local: '',
})

// Função para enviar
const submit = () => {
    form.post(route('local.registar'), {
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

function confirmarLocal() {
    if (!localSelecionado.value) {
        showWarning.value = true
        return
    }

    form.id_local = localSelecionado.value.id
    form.local = localSelecionado.value.caminho

    $('#modalBuscarLocal').modal('hide')
}

function abrirmodal(){
    localSelecionado.value = '';
    $('#modalBuscarLocal').modal('show')
}

function redirecionar_pagina(){
    router.visit(route('local'));
}

</script>

<template>
    <AuthenticatedLayout>
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Localizações/</span><strong>Registar Localização</strong></h4>

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
                            <div class="col mb-0">
                                <label for="emailLarge" class="">Tipo de Localização</label>
                                <select v-model="form.tipoLocalizacao" class="form-select">
                                    <option value="">Seleccione o tipo de localização</option>

                                    <option
                                        v-for="tlocal in tipoLocal"
                                        :key="tlocal.id"
                                        :value="tlocal.id"
                                    >
                                        {{ tlocal.nome }}
                                    </option>

                                </select>


                                <div v-if="form.errors.tipoLocalizacao" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.tipoLocalizacao }}
                                </div>
                            </div>

                        </div>

                        <div class="col mb-0">
                            <label class="">Localização</label>

                            <div class="input-group">
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="form.local"
                                    readonly
                                    placeholder="Seleccione a localização"
                                />

                                <button
                                    class="btn btn-outline-secondary"
                                    type="button"

                                    @click="abrirmodal()"
                                >
                                    <i class="bx bx-search"></i>
                                    Buscar Localização
                                </button>
                            </div>

                        </div>



                 <button type="submit" class="btn btn-primary mt-3 float-right ml-3" :disabled="form.processing">
                    {{ form.processing ? 'Enviando...' : 'Salvar' }}
                </button>
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



