<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, onMounted, computed, onUnmounted, watch  } from "vue";
import axios from 'axios';
import { useForm , usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2'

const users = ref([]);
const passo = ref(1)

function proximo() {
  if (passo.value < 2) passo.value++
}

function anterior() {
  if (passo.value > 1) passo.value--
}

//declaracao do formulario e os seus dados
const form = useForm({
    nome: '',
    codigo: '',
    descricao: '',
    qtd: '',
    imagem: null,
    valor_compra: '',
    origem: '',
    conservacao: '',
    documento: null,
    id_categoria: '',
    id_localizacao: '',
    id_estado_patrimonio: '',
    marca: '',
    cor: '',
    num_serie: '',
})

const previewImagem = ref(null)

function handleFile(event) {

    const file = event.target.files[0]

    if (!file) return

    form.imagem = file


    if (previewImagem.value) {
         alert(file)
        URL.revokeObjectURL(previewImagem.value)
    }

    previewImagem.value = URL.createObjectURL(file)
}


function handleFileDoc(event) {
   form.documento = event.target.files[0]
}

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

</script>

<template>
    <AuthenticatedLayout>
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Patrimonio/</span><strong>Registar Património</strong></h4>

        <div class="card p-4 ">

           <form @submit.prevent="submit" class="mt-3">


                <div class="row g-2 mb-3">
                    <div class="col mb-0">
                        <label for="emailLarge" class="">Nome do Património</label>
                        <input type="text" v-model="form.nome" class="form-control" />
                        <div v-if="form.errors.nome" class="text-red-500 text-sm mt-1">
                            {{ form.errors.nome }}
                        </div>
                    </div>
                    <div class="col mb-0">
                        <label for="dobLarge" class="">Código</label>
                        <input type="text" v-model="form.codigo" class="form-control"  />
                        <div v-if="form.errors.codigo" class="text-red-500 text-sm mt-1">
                            {{ form.errors.codigo }}
                        </div>
                    </div>

                    <div class="col mb-0">
                        <label for="emailLarge" class="">Quantidade (Opcional)</label>
                        <input type="password" v-model="form.qtd" class="form-control"  />
                        <div v-if="form.errors.qtd" class="text-red-500 text-sm mt-1">
                            {{ form.errors.qtd }}
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col mb-0">
                        <label for="dobLarge" class="">Custo (Opcional)</label>
                        <input type="password" v-model="form.valor_compra" class="form-control"  />
                        <div v-if="form.errors.valor_compra" class="text-red-500 text-sm mt-1">
                            {{ form.errors.valor_compra }}
                        </div>
                    </div>

                    <div class="col mb-0">
                        <label for="emailLarge" class="">Origem (Opcional)</label>
                        <input type="password" v-model="form.origem" class="form-control" />
                        <div v-if="form.errors.origem" class="text-red-500 text-sm mt-1">
                            {{ form.errors.origem }}
                        </div>
                    </div>
                    <div class="col mb-0">
                        <label for="dobLarge" class="">Conservação</label>
                        <input type="password" v-model="form.conservacao" class="form-control" />
                        <div v-if="form.errors.conservacao" class="text-red-500 text-sm mt-1">
                            {{ form.errors.conservacao }}
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col mb-0">
                        <label for="emailLarge" class="">Categoria</label>
                        <input type="password" v-model="form.id_categoria" class="form-control" />
                        <div v-if="form.errors.id_categoria" class="text-red-500 text-sm mt-1">
                            {{ form.errors.id_categoria }}
                        </div>
                    </div>
                    <div class="col mb-0">
                        <label for="dobLarge" class="">Localização</label>
                        <input type="password" v-model="form.id_localizacao" class="form-control" />
                        <div v-if="form.errors.id_localizacao" class="text-red-500 text-sm mt-1">
                            {{ form.errors.id_localizacao }}
                        </div>
                    </div>
                    <div class="col mb-0">
                        <label for="dobLarge" class="">Estado do Património</label>
                        <input type="password" v-model="form.id_estado_patrimonio" class="form-control" />
                        <div v-if="form.errors.id_estado_patrimonio" class="text-red-500 text-sm mt-1">
                            {{ form.errors.id_estado_patrimonio }}
                        </div>
                    </div>
                </div>

                    <div class="row g-2 mb-3">
                    <div class="col mb-0">
                        <label for="emailLarge" class="">Marca (Opcional)</label>
                        <input type="password" v-model="form.marca" class="form-control" />
                        <div v-if="form.errors.marca" class="text-red-500 text-sm mt-1">
                            {{ form.errors.marca }}
                        </div>
                    </div>
                    <div class="col mb-0">
                        <label for="dobLarge" class="">Série (Opcional)</label>
                        <input type="password" v-model="form.num_serie" class="form-control" />
                        <div v-if="form.errors.num_serie" class="text-red-500 text-sm mt-1">
                            {{ form.errors.num_serie }}
                        </div>
                    </div>
                    <div class="col mb-0">
                        <label for="dobLarge" class="">Cor (Opcional)</label>
                        <input type="password" v-model="form.cor" class="form-control" />
                        <div v-if="form.errors.cor" class="text-red-500 text-sm mt-1">
                            {{ form.errors.cor }}
                        </div>
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



