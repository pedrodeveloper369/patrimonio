<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>


<template>
  <div style="background-color:#E6E6E6; min-height: 100vh;"
       class="authentication-wrapper authentication-basic d-flex justify-content-center align-items-center">
    <div class="authentication-inner" style="max-width: 500px; width: 100%; padding: 2rem;">
      <slot />
    </div>
  </div>
</template>

