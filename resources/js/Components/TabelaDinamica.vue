<script setup>
import { ref, computed, watch } from 'vue';


/* ================= PROPS ================= */
const props = defineProps({
  rows: { type: Array, required: true },
  columns: { type: Array, required: true }, // [{ key, label }]
  pageSizeOptions: { type: Array, default: () => [5, 10, 20, 50] },
  defaultPageSize: { type: Number, default: 10 }
});

/* ================= EMITS ================= */
const emit = defineEmits(['delete-selected', 'row-action']);

/* ================= STATE ================= */
const search = ref('');
const pageSize = ref(props.defaultPageSize);
const currentPage = ref(1);
const selectedIds = ref([]);
const sort = ref({ key: null, dir: null });

/* ================= COMPUTED ================= */
const filteredRows = computed(() => {
  if (!search.value) return props.rows;
  return props.rows.filter(r =>
    Object.values(r).join(' ').toLowerCase().includes(search.value.toLowerCase())
  );
});

const sortedRows = computed(() => {
  if (!sort.value.key) return filteredRows.value;

  return [...filteredRows.value].sort((a, b) => {
    const A = String(a[sort.value.key]).toLowerCase();
    const B = String(b[sort.value.key]).toLowerCase();
    if (A < B) return sort.value.dir === 'asc' ? -1 : 1;
    if (A > B) return sort.value.dir === 'asc' ? 1 : -1;
    return 0;
  });
});

const totalPages = computed(() =>
  Math.max(1, Math.ceil(sortedRows.value.length / pageSize.value))
);

const paginatedRows = computed(() => {
  const start = (currentPage.value - 1) * pageSize.value;
  return sortedRows.value.slice(start, start + pageSize.value);
});

const allSelected = computed({
  get: () => selectedIds.value.length === paginatedRows.value.length && paginatedRows.value.length > 0,
  set: (val) => {
    selectedIds.value = val ? paginatedRows.value.map(r => r.id) : [];
  }
});

/* ================= METHODS ================= */
function toggleSort(key) {
  if (sort.value.key !== key) {
    sort.value = { key, dir: 'asc' };
  } else if (sort.value.dir === 'asc') {
    sort.value.dir = 'desc';
  } else {
    sort.value = { key: null, dir: null };
  }
}

function deleteSelected() {
  emit('delete-selected', selectedIds.value);
  selectedIds.value = [];
}

watch([search, pageSize], () => currentPage.value = 1);
</script>

<template>
  <!-- TOOLBAR -->
  <div class="d-flex flex-column flex-md-row gap-3 mb-4 align-items-start">

    <!-- LEFT -->
    <div v-if="selectedIds.length === 0" class="d-flex gap-2 align-items-center w-100">
      <select v-model="pageSize" class="form-select form-select-sm" style="width:auto">
        <option v-for="opt in pageSizeOptions" :key="opt" :value="opt">{{ opt }}</option>
      </select>

      <div class="position-relative flex-grow-1">
        <i class="bx bx-search position-absolute top-50 start-0 translate-middle-y ps-2"></i>
        <input
          v-model="search"
          class="form-control form-control-sm ps-5"
          placeholder="Pesquisar..."
        />
      </div>
    </div>

    <!-- RIGHT ACTIONS -->
    <div v-if="selectedIds.length === 0" class="ms-md-auto d-flex gap-2">
      <slot name="actions" />
    </div>

    <!-- DELETE -->
    <div v-else class="ms-md-auto">
      <button class="btn btn-danger btn-sm" @click="deleteSelected">
        Eliminar ({{ selectedIds.length }})
      </button>
    </div>
  </div>

  <!-- TABLE -->
  <div class="table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>
            <input type="checkbox" v-model="allSelected" />
          </th>

          <th
            v-for="col in columns"
            :key="col.key"
            @click="toggleSort(col.key)"
            style="cursor:pointer"
          >
            {{ col.label }}
            <span class="ms-1 small">
              <span v-if="sort.key === col.key && sort.dir === 'asc'">↑</span>
              <span v-if="sort.key === col.key && sort.dir === 'desc'">↓</span>
            </span>
          </th>

          <th>Ações</th>
        </tr>
      </thead>

      <tbody>
        <tr
          v-for="row in paginatedRows"
          :key="row.id"
          :class="{ 'table-primary': selectedIds.includes(row.id) }"
        >
          <td>
            <input type="checkbox" :value="row.id" v-model="selectedIds" />
          </td>

          <td v-for="col in columns" :key="col.key">
            <slot :name="`col-${col.key}`" :row="row">
              {{ row[col.key] }}
            </slot>
          </td>

          <td>
            <slot name="row-actions" :row="row" />
          </td>
        </tr>
      </tbody>
    </table>


    
  </div>

  <!-- PAGINATION -->
  <div class="d-flex justify-content-between align-items-center mt-2">
    <small class="text-muted">
      Mostrando {{ paginatedRows.length }} de {{ filteredRows.length }}
    </small>

    <div class="d-flex gap-1">
      <button class="btn btn-sm btn-outline-secondary" @click="currentPage--" :disabled="currentPage === 1">«</button>
      <button class="btn btn-sm btn-outline-secondary" @click="currentPage++" :disabled="currentPage === totalPages">»</button>
    </div>
  </div>
</template>
