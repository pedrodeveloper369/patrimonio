<template>
  <ul class="list-group ms-3">
    <li class="list-group-item border-0 d-flex align-items-center gap-2">

      <input
        type="checkbox"
        :checked="selectedId === local.id"
        @change="$emit('select', local)"
      />

      <span class="cursor-pointer" @click="$emit('select', local)">
        {{ local.nome }}
      </span>
    </li>

    <LocalTree
      v-for="child in local.children"
      :key="child.id"
      :local="child"
      :selectedId="selectedId"
      @select="$emit('select', $event)"
    />
  </ul>
</template>

<script setup>
defineProps({
  local: Object,
  selectedId: Number
})
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}
</style>
