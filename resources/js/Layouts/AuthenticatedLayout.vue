<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="layout-wrapper layout-content-navbar layout-menu-fixed" style="background-color:#E6E6E6;">
        <div class="layout-container">

            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme " >
                <div class="app-brand demo mt-2">
                    <div class="col-md-4 col-12 mb-3 mb-md-0">
                        <img src="assets/img/avatars/pitruca.webp" alt class="h-auto rounded-circle" />
                    </div>
                </div>
                <hr>
                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1 mt-3">
                    <!-- Dashboard -->
                    <li :class="['menu-item', route().current('dashboard') ? 'active' : '']"
                     v-if="$page.props.auth.permissions.includes('ver_dashboard_admin')"
                    >
                    <Link :href="route('dashboard')"  class="menu-link">
                        <i class="menu-icon tf-icons bx bx-home-circle"></i>
                        <strong>Painel de Controlo</strong>
                    </Link>
                    </li>

                     <li :class="['menu-item', route().current('patrimonio') ? 'active' : '']" >
                        <Link class="menu-link" :href="route('patrimonio')"  >
                            <i class="bx bx-archive me-2"></i>
                            <strong>Património</strong>
                        </Link>
                    </li>
                     <li :class="['menu-item', route().current('responsavel') ? 'active' : '']">
                        <Link class="menu-link" :href="route('responsavel')"  >
                           <i class="bx bx-user me-2"></i>
                            <strong>Responsáveis</strong>
                        </Link>
                    </li>

                    <li :class="['menu-item', route().current('local') ? 'active' : '']">
                        <Link class="menu-link" :href="route('local')"  >
                            <i class="bx bx-map me-2"></i>
                           <strong>Local</strong>
                        </Link>
                    </li>

                    <li :class="['menu-item', route().current('departamento') ? 'active' : '']">
                        <Link class="menu-link" :href="route('departamento')"  >

                            <i class="bx bx-building me-2"></i>
                            <strong>Departamentos</strong>
                        </Link>

                    </li>
                    <li :class="['menu-item', route().current('categoria') ? 'active' : '']">
                        <Link class="menu-link" :href="route('categoria')"  >
                           <i class="bx bx-category me-2"></i>
                            <strong>Categorias</strong>
                        </Link>
                    </li>

                   <li :class="['menu-item', route().current('movimentacao') ? 'active' : '']">
                        <Link class="menu-link" :href="route('movimentacao')"  >
                            <i class="bx bx-transfer me-2"></i>
                            <strong>Movimentações</strong>
                        </Link>
                    </li>

                    <li :class="['menu-item', route().current('relatorio') ? 'active' : '']">
                        <Link class="menu-link" :href="route('relatorio')"  >
                            <i class="bx bx-file me-2"></i>
                            <strong>Relatórios</strong>
                        </Link>
                    </li>

                <!--
                    <li :class="['menu-item', route().current('relatorio') ? 'active' : '']">
                        <a :href="route('relatorio')"  class="menu-link menu-toggle">
                            <i class="menu-icon bx bx-calendar"></i>
                            <strong>Calendário</strong>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                            <a href="extended-ui-perfect-scrollbar.html" class="menu-link">
                                <strong>Perfect scrollbar</strong>
                            </a>
                            </li>
                            <li class="menu-item">
                                <a href="extended-ui-text-divider.html" class="menu-link">
                                    <strong>Text Divider</strong>
                                </a>
                            </li>
                        </ul>
                    </li>
                    -->

                    <!--
                    <li :class="[
                            'menu-item',
                            route().current('calendario*') ? 'active open' : ''
                        ]"
                    >
                        <Link :href="route('calendario')" class="menu-link menu-toggle">
                            <i class="menu-icon bx bx-calendar"></i>
                            <strong>Calendário</strong>
                        </Link>

                            <ul class="menu-sub">
                                <li :class="['menu-item', route().current('calendario') ? 'active' : '']">
                                    <Link :href="route('calendario')" class="menu-link">
                                        <strong>Agenda</strong>
                                    </Link>
                                </li>

                                <li class="menu-item">
                                    <a href="extended-ui-perfect-scrollbar.html" class="menu-link">
                                        <strong>Perfect scrollbar</strong>
                                    </a>
                                </li>

                                <li class="menu-item">
                                    <a href="extended-ui-text-divider.html" class="menu-link">
                                        <strong>Text Divider</strong>
                                    </a>
                                </li>
                            </ul>
                        </li>

                    -->


                    <!-- Misc -->


                   <li class="menu-header small text-uppercase"><span class="menu-header-text">Configurações</span></li>
                     <!-- <li :class="['menu-item', route().current('empresa') ? 'active' : '']">
                        <Link class="menu-link" :href="route('empresa')"  >
                            <i class="menu-icon bx bx-cog"></i>
                            <strong>Dados da Empresa</strong>
                        </Link>
                    </li>-->
                    <li :class="['menu-item', route().current('configuracao') ? 'active' : '']">
                        <Link class="menu-link" :href="route('configuracao')"  >
                            <i class="menu-icon bx bx-cog"></i>
                            <strong>Configurações</strong>
                        </Link>
                    </li>

                    <li :class="['menu-item', route().current('users') ? 'active' : '']">
                        <Link class="menu-link" :href="route('users')"  >
                            <i class="menu-icon bx bx-group"></i>
                            <strong>Utilizadores</strong>
                        </Link>
                    </li>


                </ul>
            </aside>

            <div class="layout-page">
            <!-- Navbar -->
                <nav
                    class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar"
                >


                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">

                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">

                            </div>
                        </div>

                    <ul class="navbar-nav flex-row align-items-center ms-auto">

                        <!-- User -->
                        <li class="nav-item navbar-dropdown dropdown-user dropdown">
                            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                <div class="avatar avatar-online">
                                    <img src="assets/img/avatars/pitruca.webp" alt class="w-px-40 h-auto rounded-circle" />
                                </div>
                            </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <div class="dropdown-item flex-grow-1">
                                    <span class="fw-semibold d-block"> {{ $page.props.auth.user.name }}</span>
                                    <small class="text-muted">{{ $page.props.auth.user.role }}</small>
                                </div>
                            </li>
                            <li>
                            <div class="dropdown-divider"></div>
                            </li>

                            <li >
                                <Link class="dropdown-item" :href="route('configuracao')">
                                    <i class="bx bx-cog me-2"></i>
                                    <span class="align-middle">Configurações</span>
                                </Link>
                            </li>

                            <li>
                            <div class="dropdown-divider"></div>
                            </li>
                            <li>
                                <Link :href="route('logout')"  method="post" class="dropdown-item">
                                    <i class="menu-icon bx bx-log-out"></i>
                                    <span class="align-middle">Sair</span>
                                </Link>

                            </li>
                        </ul>
                        </li>
                        <!--/ User -->
                    </ul>
                    </div>
                </nav>

                <div class="content-wrapper">
                    <!-- Content -->

                    <div class="container-xxl flex-grow-1 container-p-y">
                         <!-- Page Content -->
                        <main>
                            <slot />
                        </main>
                    </div>

                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">

                            <a href="" target="_blank" class="footer-link fw-bolder">Gestão de Património</a>
                            </div>
                            <div>

                            <a
                                href=""
                                target="_blank"
                                class="footer-link me-4"
                                >Todos direitos reservados</a
                            >

                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
    </div>

</template>
