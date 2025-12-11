<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm , usePage  } from '@inertiajs/vue3';
import { ref, computed , watch, onMounted  } from "vue";
import Swal from 'sweetalert2'

//pega os dados vindo do backend
const props = defineProps({
  utilizador: Object
})

//inicializa o formulario de actualizacao dos dados
const form = useForm({
    nome: props.utilizador.name,
    contacto: props.utilizador.contacto,
})

const form2 = useForm({
    email: props.utilizador.email,
    senha: '',
    confirma_senha: '',
})


// Função para enviar
const submitPerfil = () => {
    form.post(route('configuracao.update.perfil'))
}

// Função para enviar
const submitDefinicao = () => {
    form2.post(route('configuracao.update.definicao'))
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
      timer: 6000,          // desaparece após 6 segundos
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
    <h4>Configurações</h4>

    <div class="card mb-4" style="border:1px solid #debbb3">

        <div class="card-body">
            <div class="row">
            <!-- Custom content with heading -->
            <div class="col-lg-6 mb-4 mb-xl-0">
                <div class="mt-3">
                    <div class="row">
                        <div class="col-md-4 col-12 mb-3 mb-md-0">
                            <img src="assets/img/avatars/pitruca.webp" alt class="h-auto rounded-circle" />
                        </div>
                        <div class="col-md-8 col-12">

                            <div class="tab-pane fade show " id="list-home">
                                <div class="row g-2">
                                <div class="col mb-2">
                                    <label for="emailLarge" class=""><strong style="color:#050505">Nome:</strong>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    {{ props.utilizador.name }}</label>
                                </div>

                            </div>
                            <div class="row g-2">
                                <div class="col mb-2">
                                    <label for="emailLarge" class="" > <strong style="color:#050505">E-mail:</strong>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    {{ props.utilizador.email }}</label>
                                </div>

                            </div>
                            <div class="row g-2">
                                <div class="col mb-2">
                                    <label for="emailLarge" class="" ><strong style="color:#050505">Contacto:</strong>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    {{ props.utilizador.contacto }}</label>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col mb-2">
                                    <label for="emailLarge" class=""><strong style="color:#050505">Perfil:</strong>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    {{ props.utilizador.role }} </label>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col mb-2">
                                    <label for="emailLarge" class=""><strong style="color:#050505">Estado:</strong>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    {{ props.utilizador.estado }}</label>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col mb-2">
                                    <label for="emailLarge" class="" ><strong style="color:#050505">Data de Registo:</strong>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    {{ new Date(props.utilizador.created_at).toLocaleDateString('pt-PT')}} </label>
                                </div>
                            </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <small class="text-light fw-semibold">Horizontal</small>
                <div class="demo-inline-spacing mt-3">
                <div class="list-group list-group-horizontal-md text-md-center">
                    <a
                    class="list-group-item list-group-item-action active"
                    id="profile-list-item"
                    data-bs-toggle="list"
                    href="#horizontal-profile"
                    >Perfil</a
                    >

                    <a
                    class="list-group-item list-group-item-action"
                    id="settings-list-item"
                    data-bs-toggle="list"
                    href="#horizontal-settings"
                    >Privacidade</a
                    >
                </div>
                <div class="tab-content px-0 mt-0">

                    <div class="tab-pane fade show active" id="horizontal-profile">
                        <form @submit.prevent="submitPerfil">
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

                            <button type="submit" class="btn btn-primary float-right" :disabled="form.processing">
                                {{ form.processing ? 'Actualizando...' : 'Actualizar Perfil' }}
                            </button>

                        </form>
                    </div>
                    <div class="tab-pane fade" id="horizontal-settings">
                        <form @submit.prevent="submitDefinicao">
                            <div class="row g-2 mb-3">
                                <div class="col mb-0">
                                    <label for="emailLarge" class="">Email</label>
                                    <input type="text" v-model="form2.email" class="form-control" placeholder="xxxx@xxx.xx" />
                                <div v-if="form2.errors.email" class="text-red-500 text-sm mt-1">
                                    {{ form2.errors.email }}
                                </div>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col mb-2">
                                    <label for="emailLarge" class="">Palavra Passe</label>
                                    <input type="password" v-model="form2.senha" class="form-control" placeholder="******" />
                                    <div v-if="form2.errors.senha" class="text-red-500 text-sm mt-1">
                                        {{ form2.errors.senha }}
                                    </div>
                                </div>
                                <div class="col mb-2">
                                    <label for="dobLarge" class="">Confirmar Palavra Passe</label>
                                    <input type="password" v-model="form2.confirma_senha" class="form-control" placeholder="******" />
                                    <div v-if="form2.errors.confirma_senha" class="text-red-500 text-sm mt-1">
                                        {{ form2.errors.confirma_senha }}
                                    </div>
                                </div>
                            </div>
                              <button type="submit" class="btn btn-primary float-right " :disabled="form.processing">
                                {{ form2.processing ? 'Actualizando...' : 'Actualizar' }}
                            </button>
                        </form>
                    </div>
                </div>
                </div>
            </div>
            <!--/ Custom content with heading -->
            </div>
        </div>
    </div>

  </AuthenticatedLayout>
</template>

<style scoped>

</style>
