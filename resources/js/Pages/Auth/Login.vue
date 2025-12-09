<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <div class="card">
            <div class="card-body">
                <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                    {{ status }}
                </div>
                <!-- Logo -->
                <div class="app-brand justify-content-center">
                    <a href="index.html" class="app-brand-link gap-2">
                    <span class="app-brand-logo demo">
                        <div class="col-md-4 col-12 mb-3 mb-md-0">
                            <img src="assets/img/avatars/pitruca.webp" alt class="h-auto rounded-circle" />
                        </div>
                     </span>
                    </a>
                </div>
                <!-- /Logo -->
                <h5 class="mb-2">Bem-vindo ao Sis Gestão de Património</h5>

                <form @submit.prevent="submit" id="formAuthentication" class="mb-3">
                    <div class="mb-3">
                        <InputLabel for="email" class="form-label" value="Email" />

                        <TextInput
                            id="email"
                            type="email"
                            class="form-control"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Informe o seu email"
                        />

                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>


                    <div class="mt-3">
                        <InputLabel for="password" class="form-label" value="Senha" />

                        <TextInput
                            id="password"
                            type="password"
                            class="form-control"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                            aria-describedby="password"
                        />

                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                   <!-- <div class="mt-4 block">
                        <label class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.remember" />
                            <span class="ms-2 text-sm text-gray-600"
                                >Remember me</span
                            >
                        </label>
                    </div>-->

                    <div class="mt-4 flex items-center ">
                       <!--   <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Forgot your password?
                        </Link>-->


                         <button class="form-control btn btn-primary " :disabled="form.processing" :class="{ 'opacity-25': form.processing } ">
                            Entrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>
