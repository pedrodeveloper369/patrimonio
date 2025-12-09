<script setup>
import { defineProps, defineEmits } from "vue";

const props = defineProps({
  columns: Array,
  paginatedRows: Array,
  filteredRows: Array,

  selected: Array,
  selectAll: Boolean,

  currentPage: Number,
  totalPages: Number,

  prevPage: Function,
  nextPage: Function,
  goToPage: Function,
});

const emit = defineEmits(["update:selected", "update:selectAll"]);

// Seleção de todos
function toggleAll(event) {
  emit("update:selectAll", event.target.checked);
}

// Seleção individual
function toggleRow(id) {
  let newSelected = [...props.selected];

  if (newSelected.includes(id)) {
    newSelected = newSelected.filter((x) => x !== id);
  } else {
    newSelected.push(id);
  }

  emit("update:selected", newSelected);
}
</script>

<template>
  <table class="table table-hover table-bordered">
    <thead>
      <tr>
        <th style="width: 30px;">
          <input type="checkbox" :checked="selectAll" @change="toggleAll" />
        </th>

        <th v-for="col in columns" :key="col.key">
          {{ col.label }}
        </th>
      </tr>
    </thead>

    <tbody>
      <tr v-for="row in paginatedRows" :key="row.id">
        <td>
          <input
            type="checkbox"
            :checked="selected.includes(row.id)"
            @change="toggleRow(row.id)"
          />
        </td>

        <td v-for="col in columns" :key="col.key">
          {{ row[col.key] }}
        </td>
      </tr>

      <tr v-if="paginatedRows.length === 0">
        <td colspan="20" class="text-center py-3">
          Nenhum registro encontrado.
        </td>
      </tr>
    </tbody>
  </table>

  <!-- PAGINAÇÃO -->
  <div class="d-flex justify-content-between align-items-center mt-3">
    <button class="btn btn-sm btn-secondary" @click="prevPage" :disabled="currentPage === 1">
      Prev
    </button>

    <span>Página {{ currentPage }} / {{ totalPages }}</span>

    <button class="btn btn-sm btn-secondary" @click="nextPage" :disabled="currentPage === totalPages">
      Next
    </button>
  </div>
</template>
