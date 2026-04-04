<template>
  <ul class="list-group ms-3">
    <li class="list-group-item border-0 d-flex align-items-center gap-2">

      <!-- Botão de expandir/recolher -->
      <span
        v-if="local.children && local.children.length"
        @click="expanded = !expanded"
        class="cursor-pointer fw-bold me-1"
      >
        {{ expanded ? '-' : '+' }}
      </span>
      <span v-else style="width: 12px; display: inline-block;"></span>

      <!-- Checkbox de seleção -->
      <input
        type="checkbox"
        :checked="selectedId === local.id"
        @change="$emit('select', local)"
      />

      <!-- Nome do local -->
      <span class="cursor-pointer" @click="$emit('select', local)">
        {{ local.nome }}
      </span>
    </li>

    <!-- Renderiza filhos apenas se expandido -->
    <template v-if="expanded">
      <LocalTree
        v-for="child in local.children"
        :key="child.id"
        :local="child"
        :selectedId="selectedId"
        @select="$emit('select', $event)"
      />
    </template>
  </ul>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  local: Object,
  selectedId: Number
})

const expanded = ref(false)
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}
</style>
