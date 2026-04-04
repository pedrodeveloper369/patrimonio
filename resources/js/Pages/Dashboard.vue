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


const props = defineProps({
    departamentoTotal: Number,
    responsavelTotal: Number,
    categoriaTotal: Number,
    localTotal: Number,
    movimentacaoTotal: Number,
    patrimonioTotal: Number,
    patrimonioRecentes: Array,
});

const departamentoTotal = ref(props.departamentoTotal);
const responsavelTotal = ref(props.responsavelTotal);
const categoriaTotal = ref(props.categoriaTotal);
const localTotal = ref(props.localTotal);
const movimentacaoTotal = ref(props.movimentacaoTotal);
const patrimonioTotal = ref(props.patrimonioTotal);
const patrimonioRecentes = ref(props.patrimonioRecentes);

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
        <h4 class=""><strong>Painel de Controlo</strong></h4>
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                  <div class="card" >
                    <div class="d-flex align-items-end row">
                      <div class="col-sm-8">
                        <div class="card-body" >
                          <img src="assets/img/avatars/pitruca.webp" style="height:150px"/>
                        </div>
                      </div>

                      <div class="col-sm-4 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4 float-right">
                          <img style="height:150px"
                            src="assets/img/illustrations/man-with-laptop-light.png"
                            alt="View Badge User"
                            data-app-dark-img="illustrations/man-with-laptop-dark.png"
                            data-app-light-img="illustrations/man-with-laptop-light.png"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
            </div>

             <h5 class="mt-3"><strong>Dados estatísticos</strong></h5>

            <div class="col-lg-12 col-md-4 order-1">
                <div class="row">
                    <div class="col-lg-2 col-md-4 col-6 mb-4" >
                        <div class="card" >
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img
                                        src="assets/img/icons/unicons/cc-primary.png"
                                        alt="chart success"
                                        class="rounded"
                                        />
                                    </div>
                                </div>
                                <span class="fw-semibold d-block mb-1">Patrimónios</span>
                                <h3 class="card-title mb-2">{{patrimonioTotal}}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-12 col-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img
                                        src="assets/img/icons/unicons/cc-success.png"
                                        alt="chart success"
                                        class="rounded"
                                        />
                                    </div>

                                </div>
                                <span class="fw-semibold d-block mb-1">Responsaveis</span>
                                <h3 class="card-title mb-2">{{ responsavelTotal }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-12 col-6 mb-4">
                        <div class="card" >
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img
                                        src="assets/img/icons/unicons/cc-warning.png"
                                        alt="chart success"
                                        class="rounded"
                                        />
                                    </div>
                                </div>
                                <span class="fw-semibold d-block mb-1">Localizações</span>
                                <h3 class="card-title mb-2">{{localTotal}}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-12 col-6 mb-4">
                        <div class="card" >
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img
                                        src="assets/img/icons/unicons/wallet.png"
                                        alt="chart success"
                                        class="rounded"
                                        />
                                    </div>

                                </div>
                                <span class="fw-semibold d-block mb-1">Departamentos</span>
                                <h3 class="card-title mb-2">{{departamentoTotal}}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-12 col-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img
                                        src="assets/img/icons/unicons/wallet-info.png"
                                        alt="chart success"
                                        class="rounded"
                                        />
                                    </div>

                                </div>
                                <span class="fw-semibold d-block mb-1">Categorias</span>
                                <h3 class="card-title mb-2">{{categoriaTotal}}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-12 col-6 mb-4">
                        <div class="card" >
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img
                                        src="assets/img/icons/unicons/chart-success.png"
                                        alt="chart success"
                                        class="rounded"
                                        />
                                    </div>

                                </div>
                                <span class="fw-semibold d-block mb-1">Movimentações</span>
                                <h3 class="card-title mb-2">{{movimentacaoTotal}}</h3>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card" >
                <h5 class="card-header">Patrimónios Recentes</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table table-hover table-striped">
                    <thead>
                      <tr>
                       <th>Foto</th>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Estado</th>
                        <th>Localização</th>
                        <th>Responsável</th>
                        <th>Custo</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <tr v-for="pat in patrimonioRecentes" :key="pat.id" :data-id="pat.id" >
                            <td>
                                 <img
                                    v-if="pat.imagem"
                                    :src="`/storage/patrimonios/imagens/${pat.imagem}`"
                                    alt="Imagem do Património"
                                    style="width:50px; height:auto; border-radius:9px"
                                >
                                <img
                                    v-else
                                    src="/assets/img/avatars/pitruca.webp"
                                    alt="Imagem padrão"
                                    style="width:50px; height:auto; border-radius:9px"
                                >
                            </td>
                            <td>{{pat.codigo}}</td>
                            <td>{{pat.nome}}</td>
                            <td>{{pat.categoria}}</td>
                            <td>
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded"
                                    :style="{ backgroundColor: pat.background, color: pat.cor }"

                                >
                                    {{ pat.estado_patrimonio }}
                                </span>
                            </td>
                            <td>{{pat.localizacao}}</td>
                            <td>{{pat.responsavel}}</td>
                            <td>{{pat.valor_compra}} kz</td>
                        </tr>
                    </tbody>
                  </table>
                </div>
        </div>


        <!--<div class="row mt-3">

            <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between pb-0">
                        <div class="card-title mb-0">
                            <h5 class="m-0 me-2">Top 10 Patrimónios mais movimentados</h5>
                            <small class="text-muted">42.82 k Total Sales</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3"></div>
                        <ul class="p-0 m-0">
                            <li class="d-flex mb-4 pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary"
                                    ><i class="bx bx-mobile-alt"></i
                                ></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Electronic</h6>
                                    <small class="text-muted">Mobile, Earbuds, TV</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">82.5k</small>
                                </div>
                                </div>
                            </li>
                            <li class="d-flex mb-4 pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success"><i class="bx bx-closet"></i></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Fashion</h6>
                                    <small class="text-muted">T-shirt, Jeans, Shoes</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">23.8k</small>
                                </div>
                                </div>
                            </li>
                            <li class="d-flex mb-4 pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info"><i class="bx bx-home-alt"></i></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Decor</h6>
                                    <small class="text-muted">Fine Art, Dining</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">849k</small>
                                </div>
                                </div>
                            </li>
                            <li class="d-flex">
                                <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-secondary"
                                    ><i class="bx bx-football"></i
                                ></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Sports</h6>
                                    <small class="text-muted">Football, Cricket Kit</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">99</small>
                                </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>



            <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between pb-0">
                        <div class="card-title mb-0">
                            <h5 class="m-0 me-2">Top 10 Responsaveis com mais patrimónios</h5>
                            <small class="text-muted">42.82 k Total Sales</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3"></div>
                        <ul class="p-0 m-0">
                            <li class="d-flex mb-4 pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary"
                                    ><i class="bx bx-mobile-alt"></i
                                ></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Electronic</h6>
                                    <small class="text-muted">Mobile, Earbuds, TV</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">82.5k</small>
                                </div>
                                </div>
                            </li>
                            <li class="d-flex mb-4 pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success"><i class="bx bx-closet"></i></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Fashion</h6>
                                    <small class="text-muted">T-shirt, Jeans, Shoes</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">23.8k</small>
                                </div>
                                </div>
                            </li>
                            <li class="d-flex mb-4 pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info"><i class="bx bx-home-alt"></i></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Decor</h6>
                                    <small class="text-muted">Fine Art, Dining</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">849k</small>
                                </div>
                                </div>
                            </li>
                            <li class="d-flex">
                                <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-secondary"
                                    ><i class="bx bx-football"></i
                                ></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Sports</h6>
                                    <small class="text-muted">Football, Cricket Kit</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">99</small>
                                </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>



            <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between pb-0">
                        <div class="card-title mb-0">
                            <h5 class="m-0 me-2">Top 10 Localizações com mais movimentados</h5>
                            <small class="text-muted">42.82 k Total Sales</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3"></div>
                        <ul class="p-0 m-0">
                            <li class="d-flex mb-4 pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary"
                                    ><i class="bx bx-mobile-alt"></i
                                ></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Electronic</h6>
                                    <small class="text-muted">Mobile, Earbuds, TV</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">82.5k</small>
                                </div>
                                </div>
                            </li>
                            <li class="d-flex mb-4 pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success"><i class="bx bx-closet"></i></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Fashion</h6>
                                    <small class="text-muted">T-shirt, Jeans, Shoes</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">23.8k</small>
                                </div>
                                </div>
                            </li>
                            <li class="d-flex mb-4 pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info"><i class="bx bx-home-alt"></i></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Decor</h6>
                                    <small class="text-muted">Fine Art, Dining</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">849k</small>
                                </div>
                                </div>
                            </li>
                            <li class="d-flex">
                                <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-secondary"
                                    ><i class="bx bx-football"></i
                                ></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Sports</h6>
                                    <small class="text-muted">Football, Cricket Kit</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">99</small>
                                </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>-->

    </AuthenticatedLayout>
</template>

<style scoped>
</style>
