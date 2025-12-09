<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div style="background-color:#E6E6E6;">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <slot />
            </div>
        </div>
    </div>
</template>
