<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Link } from '@inertiajs/vue3';

</script>

<template>
  <GuestLayout>
    <div class="container-xxl container-p-y">
      <div class="misc-wrapper">
        <h2 class="mb-2 mx-2">Pagina não encontrada </h2>
        <p class="mb-4 mx-2">Oops! 😖 A requisição solicitada não foi encontrada.</p>
        <Link :href="route('dashboard')" class="btn btn-primary">Voltar ao Painel de controlo</Link>
        <div class="mt-3">
          <img
            src="/assets/img/illustrations/page-misc-error-light.png"
            alt="page-misc-error-light"
            width="500"
            class="img-fluid"
            data-app-dark-img="illustrations/page-misc-error-dark.png"
            data-app-light-img="illustrations/page-misc-error-light.png"
          />
        </div>
      </div>
    </div>

  </GuestLayout>
</template>

<style scoped>

</style>
