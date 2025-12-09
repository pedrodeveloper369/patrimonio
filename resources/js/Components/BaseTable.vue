<script setup>
import { ref, computed, watch, defineEmits, defineProps } from 'vue';

// Props que o componente recebe
const props = defineProps({
  rows: { type: Array, default: () => [] },
  columns: { type: Array, default: () => [] },
});

// Eventos que o componente emite
const emit = defineEmits(['delete-selected', 'row-action']);

// Estados internos
const selectAll = ref(false);
const selected = ref([]);
const search = ref('');
const pageSize = ref(10);
const currentPage = ref(1);

const pageSizeOptions = [5, 10, 20, 50];

// Computed: filtra linhas por busca
const filteredRows = computed(() => {
  if (!search.value) return props.rows;
  const s = search.value.toLowerCase();
  return props.rows.filter(r =>
    Object.values(r).join(' ').toLowerCase().includes(s)
  );
});

// Computed: paginacao
const paginatedRows = computed(() => {
  const start = (currentPage.value - 1) * pageSize.value;
  return filteredRows.value.slice(start, start + pageSize.value);
});

const totalPages = computed(() =>
  Math.ceil(filteredRows.value.length / pageSize.value)
);

// Paginação com limite de páginas visíveis
const maxPagesToShow = 5;
const pageNumbers = computed(() => {
  const total = totalPages.value;
  const current = currentPage.value;
  let pages = [];
  if (total <= maxPagesToShow + 2) {
    for (let i = 1; i <= total; i++) pages.push(i);
  } else {
    pages.push(1);
    let startPage = Math.max(current - 1, 2);
    let endPage = Math.min(current + 1, total - 1);
    if (current <= 3) { startPage = 2; endPage = 4; }
    if (current >= total - 2) { startPage = total - 3; endPage = total - 1; }
    if (startPage > 2) pages.push('...');
    for (let i = startPage; i <= endPage; i++) pages.push(i);
    if (endPage < total - 1) pages.push('...');
    pages.push(total);
  }
  return pages;
});

// Métodos
function toggleSelectAll() {
  selected.value = selectAll.value ? props.rows.map(r => r.id) : [];
}

function goToPage(p) {
  if (p >= 1 && p <= totalPages.value) currentPage.value = p;
}
function nextPage() { if (currentPage.value < totalPages.value) currentPage.value++; }
function prevPage() { if (currentPage.value > 1) currentPage.value--; }

function deleteSelected() {
  emit('delete-selected', selected.value);
  selected.value = [];
  selectAll.value = false;
}

function rowAction(action, row) {
  emit('row-action', { action, row });
}

// Watches
watch(selected, (val) => {
  if (val.length === props.rows.length) selectAll.value = true;
  else if (val.length === 0) selectAll.value = false;
});
watch(pageSize, () => { currentPage.value = 1; });
watch(filteredRows, () => { if (currentPage.value > totalPages.value) currentPage.value = 1; });

</script>

<template>
  <div>
    <!-- Ações: pesquisa e pageSize -->
    <!-- TOOLBAR RESPONSIVA -->
    <div class="d-flex flex-column flex-md-row w-100 gap-2 align-items-start">

        <!-- QUANDO NÃO HÁ SELECIONADOS -->
        <div
            v-if="selected.length === 0"
            class="d-flex flex-column flex-md-row w-100 gap-2 align-items-start"
        >

            <!-- ESQUERDA: pageSize + search -->
            <div class="d-flex flex-column flex-md-row gap-2 w-100">
                <select
                    v-model="pageSize"
                    class="form-select form-select-sm"
                    style="width: auto;"
                >
                    <option v-for="opt in pageSizeOptions" :key="opt" :value="opt">
                        {{ opt }}
                    </option>
                </select>

                <input
                    v-model="search"
                    placeholder="Pesquisar..."
                    class="form-control form-control-sm w-100 w-md-auto"
                    style="max-width: 300px;"

                />
            </div>

            <!-- DIREITA: botões via slot -->
            <div
                class="d-flex flex-column flex-md-row gap-2 ms-md-auto"
                style="min-width: max-content;"
            >
                <slot name="actions"></slot>
            </div>

        </div>

        <!-- QUANDO HÁ SELECIONADOS -->
        <div
            v-else
            class="d-flex flex-column flex-md-row ms-md-auto gap-2"
            style="min-width: max-content;"
        >
            <slot name="delete-action" :count="selected.length" :ids="selected">
                <button class="btn btn-danger btn-sm">
                    Eliminar ({{ selected.length }})
                </button>
            </slot>
        </div>

    </div>


    <div class="table-responsive mt-6">
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th>
                    <input type="checkbox" v-model="selectAll" @change="toggleSelectAll" />
                </th>
                <th v-for="col in props.columns" :key="col.key">{{ col.label }}</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="row in paginatedRows" :key="row.id" :class="{ 'table-primary': selected.includes(row.id) }">
                <td><input type="checkbox" :value="row.id" v-model="selected" /></td>
                <!--<td v-for="col in props.columns" :key="col.key">{{ row[col.key] }}</td>-->

                <td v-for="col in props.columns" :key="col.key">
                    <slot
                        :name="`col-${col.key}`"
                        :value="row[col.key]"
                        :row="row"
                    >
                        {{ row[col.key] }}
                    </slot>
                </td>

                <td>
                    <button @click="rowAction('view', row)" class="btn btn-sm btn-outline-primary">Ver</button>
                    <button @click="rowAction('delete', row)" class="btn btn-sm btn-outline-secondary">Editar</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="d-flex justify-content-between align-items-center mt-2">
    <div>Total: {{ filteredRows.length }} registros</div>
    <div class="d-flex gap-1">
        <button @click="prevPage" :disabled="currentPage === 1" class="btn btn-sm btn-outline-secondary">«</button>
        <button v-for="p in pageNumbers" :key="p" @click="goToPage(p)" :class="['btn btn-sm', p === currentPage ? 'btn-primary text-white' : 'btn-outline-secondary']">
        {{ p }}
        </button>
        <button @click="nextPage" :disabled="currentPage === totalPages" class="btn btn-sm btn-outline-secondary">»</button>
    </div>
    </div>


  </div>
</template>

<style scoped>
.table-responsive { max-height: 500px; overflow-y: auto; }
</style>
