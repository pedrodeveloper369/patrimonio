<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed , watch, onMounted  } from "vue";
// ==========================================
// MOCK DE UTILIZADORES (para testes)
// ==========================================
const rows = ref([
  {
    id: 1,
    name: "João Pedro",
    email: "joao@example.com",
    role: "Admin",
    status: "Active",
    plan: "Premium",
    created_at: "2025-01-10",
  },
  {
    id: 2,
    name: "Maria Silva",
    email: "maria@example.com",
    role: "User",
    status: "Inactive",
    plan: "Basic",
    created_at: "2025-02-04",
  },
  {
    id: 3,
    name: "Carlos Alberto",
    email: "carlos@example.com",
    role: "Manager",
    status: "Pending",
    plan: "Enterprise",
    created_at: "2025-02-15",
  },
  {
    id: 4,
    name: "Ana Paula",
    email: "ana@example.com",
    role: "User",
    status: "Active",
    plan: "Premium",
    created_at: "2025-03-02",
  }
  ,
  {
    id: 5,
    name: "Ana Paula",
    email: "ana@example.com",
    role: "User",
    status: "Active",
    plan: "Premium",
    created_at: "2025-03-02",
  }
  ,
  {
    id: 6,
    name: "Ana Paula",
    email: "ana@example.com",
    role: "User",
    status: "Active",
    plan: "Premium",
    created_at: "2025-03-02",
  }
  ,
  {
    id: 7,
    name: "Ana Paula",
    email: "ana@example.com",
    role: "User",
    status: "Active",
    plan: "Premium",
    created_at: "2025-03-02",
  }
  ,
  {
    id: 8,
    name: "Ana Paula",
    email: "ana@example.com",
    role: "User",
    status: "Active",
    plan: "Premium",
    created_at: "2025-03-02",
  }
  ,
  {
    id: 9,
    name: "Ana Paula",
    email: "ana@example.com",
    role: "User",
    status: "Active",
    plan: "Premium",
    created_at: "2025-03-02",
  },
  {
    id: 10,
    name: "Maria Silva",
    email: "maria@example.com",
    role: "User",
    status: "Inactive",
    plan: "Basic",
    created_at: "2025-02-04",
  },
  {
    id: 11,
    name: "Maria Silva",
    email: "maria@example.com",
    role: "User",
    status: "Inactive",
    plan: "Basic",
    created_at: "2025-02-04",
  },
   {
    id: 12,
    name: "Maria Silva",
    email: "maria@example.com",
    role: "User",
    status: "Inactive",
    plan: "Basic",
    created_at: "2025-02-04",
  },
]);

// ==========================================
// COLUNAS DA TABELA
// ==========================================
const columns = [
  { label: "Name", key: "name" },
  { label: "Role", key: "role" },
  { label: "Status", key: "status" },
  { label: "Plan", key: "plan" },
  { label: "Created At", key: "created_at" }
];


// ==========================================
// ESTADOS DOS FILTROS
// ==========================================
const filterStatus = ref("");
const filterRole = ref("");
const filterPlan = ref("");
const filterDate = ref("");

const search = ref("");
const pageSize = ref(10);
const currentPage = ref(1);

const pageSizeOptions = [5,10, 20, 50];

// ==========================================
// CHECKBOX GERAL
// ==========================================
const selectAll = ref(false);
const selected = ref([]);

function toggleSelectAll() {
  if (selectAll.value) {
    selected.value = rows.value.map((r) => r.id);
  } else {
    selected.value = [];
  }
}



// ==========================================
// FILTRAGEM REAL
// ==========================================
const filteredRows = computed(() => {
  let result = rows.value;

  if (filterStatus.value) {
    result = result.filter(r => r.status === filterStatus.value);
  }

  if (filterRole.value) {
    result = result.filter(r => r.role === filterRole.value);
  }

  if (filterPlan.value) {
    result = result.filter(r => r.plan === filterPlan.value);
  }

  if (filterDate.value) {
    result = result.filter(r => r.created_at === filterDate.value);
  }

  if (search.value) {
    const s = search.value.toLowerCase();
    result = result.filter(r =>
      Object.values(r).join(" ").toLowerCase().includes(s)
    );
  }

  return result;
});

// ==========================================
// PAGINAÇÃO
// ==========================================
const paginatedRows = computed(() => {
  const start = (currentPage.value - 1) * pageSize.value;
  return filteredRows.value.slice(start, start + pageSize.value);
});

const totalPages = computed(() =>
  Math.ceil(filteredRows.value.length / pageSize.value)
);

// ==== PAGINAÇÃO PROFISSIONAL COM 5 PÁGINAS POR BLOCO ====
const maxPagesToShow = 5;

const pageNumbers = computed(() => {
  const total = totalPages.value;
  const current = currentPage.value;
  const maxVisiblePages = 5; // número máximo de páginas centrais visíveis
  let pages = [];

  if (total <= maxVisiblePages + 2) {
    // poucas páginas, mostra todas
    for (let i = 1; i <= total; i++) pages.push(i);
  } else {
    // sempre mostra primeira e última
    pages.push(1);

    let startPage = Math.max(current - 1, 2);
    let endPage = Math.min(current + 1, total - 1);

    // Ajustes quando perto do início ou do fim
    if (current <= 3) {
      startPage = 2;
      endPage = 4;
    } else if (current >= total - 2) {
      startPage = total - 3;
      endPage = total - 1;
    }

    if (startPage > 2) pages.push('...');
    for (let i = startPage; i <= endPage; i++) pages.push(i);
    if (endPage < total - 1) pages.push('...');

    pages.push(total);
  }

  return pages;
});




function goToPage(p) {
  if (p >= 1 && p <= totalPages.value) {
    currentPage.value = p;
  }
}

function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}
function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}

watch(pageSize, () => {
  currentPage.value = 1;
});

watch(filteredRows, () => {
  if (currentPage.value > totalPages.value) {
    currentPage.value = 1;
  }
});

// Sempre que `selected` mudar, atualiza `selectAll`
watch(selected, (val) => {
  if (val.length === rows.value.length) {
    selectAll.value = true;

  } else if (val.length > 0 && val.length < rows.value.length){

  } else{
    selectAll.value = false;

  }
});

function deleteSelected() {
  if (selected.value.length === 0) return;

  // Remove elementos selecionados
  rows.value = rows.value.filter(row => !selected.value.includes(row.id));

  // Limpa seleção
  selected.value = [];
  selectAll.value = false;
}


</script>

<template>
    <AuthenticatedLayout>
        <div class="card p-4 ">
            <h4>Gerenciamento de Leads</h4>
            <!-- Controles: filtros + ações / botão eliminar -->
            <div class="d-flex flex-column flex-md-row align-items-start mt-3 w-100 gap-2">

                <!-- Botão Eliminar (aparece apenas se houver seleção) -->
                <div v-if="selected.length > 0" class="ms-auto">
                    <button
                    @click="deleteSelected"
                    class="btn btn-danger btn-sm"
                    >
                    Eliminar ({{ selected.length }})
                    </button>
                </div>

                <!-- Toda a seção de filtros + pesquisa + botões, ocultada se houver seleção -->
                <template v-else>

                    <!-- Filtros -->
                    <div class="d-flex flex-column flex-md-row gap-2 w-100">
                        <select v-model="filterStatus" class="form-select search form-select-sm equal-height">
                            <option value="">Status</option>
                            <option>Active</option>
                            <option>Inactive</option>
                            <option>Pending</option>
                        </select>

                        <select v-model="filterRole" class="form-select form-select-sm equal-height">
                            <option value="">Role</option>
                            <option>Admin</option>
                            <option>User</option>
                            <option>Manager</option>
                        </select>

                        <select v-model="filterPlan" class="form-select form-select-sm equal-height">
                            <option value="">Plan</option>
                            <option>Basic</option>
                            <option>Premium</option>
                            <option>Enterprise</option>
                        </select>
                    </div>

                    <!-- Ações: pesquisa, pageSize, Exportar, Adicionar -->
                    <div class="d-flex flex-column flex-md-row gap-2 w-100 mt-2 mt-md-0 ms-md-auto">
                        <select v-model="pageSize" class="form-select form-select-sm equal-height small-width">
                            <option v-for="opt in pageSizeOptions" :key="opt" :value="opt">
                                {{ opt }}
                            </option>
                        </select>

                        <input
                            v-model="search"
                            placeholder="Pesquisar"
                            class="form-control form-control-sm equal-height"
                        />

                        <button class="btn btn-outline-secondary btn-sm equal-height">
                            Exportar
                        </button>

                        <button class="btn btn-primary btn-sm equal-height" style="min-width: 90px;">
                            + Adicionar
                        </button>
                    </div>

                </template>

            </div>
            <div class="table-responsive text-nowrap mt-3">
                <!--  TABELA -->
                <table class="min-w-full  mt-6 text-sm" style="text-align:lef">
                    <thead class="bg-gray-100 ">
                        <tr class="border-b">
                        <th class="py-3 px-2" >
                            <input class=""  ref="headerCheckbox" type="checkbox" v-model="selectAll" @change="toggleSelectAll" style="border-radius:20%"/>
                        </th>

                        <th v-for="col in columns" :key="col.key" class="py-3 px-2 font-semibold text-gray-600">
                            {{ col.label }}
                        </th>

                        <th class="py-3 px-2 font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="row in paginatedRows" :key="row.id"  :class="{ 'bg-blue-100': selected.includes(row.id) }" class="border-b hover:bg-gray-50">

                            <td class="p-3 text-left">
                                <input type="checkbox" :value="row.id" v-model="selected" style="border-radius:20%"/>
                            </td>

                            <!-- NAME + EMAIL -->
                            <td class="p-3 text-left">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-800">{{ row.name }}</span>
                                    <span class="text-xs text-gray-500">{{ row.email }}</span>
                                </div>
                            </td>

                            <!-- ROLE -->
                            <td class="p-3 text-left">
                                <span class="px-2 py-1 text-xs rounded text-gray-700">
                                    {{ row.role }}
                                </span>
                            </td>



                            <!-- PLAN -->
                            <td class="p-3 text-left">
                                <span class="px-2 py-1 text-xs rounded">
                                    {{ row.plan }}
                                </span>
                            </td>

                            <!-- CREATED AT -->
                            <td class="p-3 text-left">
                                <span class="text-xs text-gray-600">{{ row.created_at }}</span>
                            </td>

                            <td class="p-3 text-gray-700 flex gap-3">
                                <button >View</button>
                                <button >Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!-- PAGINAÇÃO FINAL -->
                <div class="flex items-center justify-between mt-6 px-2">

                    <!-- TOTAL DE REGISTOS (ESQUERDA) -->
                    <div class="text-gray-600 text-sm">
                        Mostrando
                        <strong>{{ paginatedRows.length }}</strong>
                        de
                        <strong>{{ filteredRows.length }}</strong>
                        registos
                    </div>



                <!-- CONTROLES (DIREITA) -->
                <div class="flex items-center gap-1">

                    <!-- Anterior -->
                    <button
                    @click="prevPage"
                    :disabled="currentPage === 1"
                    class="px-3 py-1 border rounded disabled:opacity-40"
                    >
                    «
                    </button>

                    <!-- Números das páginas -->
                    <button
                    v-for="p in pageNumbers"
                    :key="p"
                    @click="goToPage(p)"
                    class="px-3 py-1 border rounded"
                    :class="p === currentPage ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                    >
                    {{ p }}
                    </button>

                    <!-- Próxima -->
                    <button
                    @click="nextPage"
                    :disabled="currentPage === totalPages"
                    class="px-3 py-1 border rounded disabled:opacity-40"
                    >
                    »
                    </button>

                </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
