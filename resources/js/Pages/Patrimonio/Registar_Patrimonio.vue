<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, onMounted, computed, onUnmounted, watch  } from "vue";
import axios from 'axios';
import { useForm , usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2'

const users = ref([]);
const passo = ref(1)

function proximo() {
  if (passo.value < 3) passo.value++
}

function anterior() {
  if (passo.value > 1) passo.value--
}


//declaracao do formulario e os seus dados
const form = useForm({
    nome: '',
    contacto: '',
    email: '',
    senha: '',
    confirma_senha: '',
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

</script>

<template>
    <AuthenticatedLayout>
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Patrimonio/</span><strong>Registar Património</strong></h4>

        <div class="card p-4 " >
            <div class="d-flex align-items-center justify-content-between mb-4 stepper">

                <div class="step" :class="{ active: passo >= 1 }">
                    <div class="circle">1</div>
                    <span>Informações Gerais</span>
                </div>

                <div class="line" :class="{ active: passo >= 2 }"></div>

                <div class="step" :class="{ active: passo >= 2 }">
                    <div class="circle">2</div>
                    <span>Anexos</span>
                </div>

                <div class="line" :class="{ active: passo >= 3 }"></div>

                <div class="step" :class="{ active: passo >= 3 }">
                    <div class="circle">3</div>
                    <span>Confirmação</span>
                </div>

            </div>


           <form @submit.prevent="submit">

               <div v-if="passo === 1">
                    <div class="row mb-3">
                        <div class="col">
                        <label>Nome</label>
                        <input type="text" v-model="form.nome" class="form-control" />
                        <div v-if="form.errors.nome" class="text-danger text-sm">
                            {{ form.errors.nome }}
                        </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                        <label>Contacto</label>
                        <input type="text" v-model="form.contacto" class="form-control" />
                        <div v-if="form.errors.contacto" class="text-danger text-sm">
                            {{ form.errors.contacto }}
                        </div>
                        </div>
                    </div>
                </div>

                <div v-if="passo === 2">
                    <div class="row mb-3">
                        <div class="col">
                        <label>Email</label>
                        <input type="email" v-model="form.email" class="form-control" />
                        <div v-if="form.errors.email" class="text-danger text-sm">
                            {{ form.errors.email }}
                        </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                        <label>Palavra Passe</label>
                        <input type="password" v-model="form.senha" class="form-control" />
                        </div>

                        <div class="col">
                        <label>Confirmar Palavra Passe</label>
                        <input type="password" v-model="form.confirma_senha" class="form-control" />
                        </div>
                    </div>
                    <div v-if="form.errors.senha || form.errors.confirma_senha" class="text-danger text-sm">
                        {{ form.errors.senha || form.errors.confirma_senha }}
                    </div>
                </div>
                <div v-if="passo === 3">
                    <h6 class="mb-3">Confirme os dados</h6>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Nome:</strong> {{ form.nome }}</li>
                        <li class="list-group-item"><strong>Contacto:</strong> {{ form.contacto }}</li>
                        <li class="list-group-item"><strong>Email:</strong> {{ form.email }}</li>
                    </ul>
                </div>
                <div class="mt-4 float-right ">
                    <button
                        type="button"
                        class="btn btn-outline-secondary mr-2"
                        @click="anterior"
                        v-if="passo > 1"
                    >
                        Voltar
                    </button>

                    <button
                        type="button"
                        class="btn btn-warning ms-auto"
                        @click="proximo"
                        v-if="passo < 3"
                    >
                        Próximo
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary ms-auto"
                        v-if="passo === 3"
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

.stepper {
  width: 100%;
}

.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  min-width: 120px;
  color: #adb5bd;
  font-size: 0.85rem;
}

.step.active {
  color: #fcb03f;
}

.circle {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 2px solid #adb5bd;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  margin-bottom: 6px;
  background: #fff;
}

.step.active .circle {
  border-color: #fcb03f;
  background: #fcb03f;
  color: #fff;
}

.line {
  flex: 1;
  height: 2px;
  background: #dee2e6;
}

.line.active {
  background: #fcb03f;
}


</style>



