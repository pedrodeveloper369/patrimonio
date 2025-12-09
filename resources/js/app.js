import '../css/app.css';
import './bootstrap';

import { createInertiaApp, Head, Link, router  } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

import '../../public/assets/vendor/fonts/boxicons.css';
import '../../public/assets/vendor/css/core.css';
import '../../public/assets/vendor/css/pages/page-auth.css';
import '../../public/assets/vendor/css/theme-default.css';

// JS Helpers e config
import '../../public/assets/vendor/js/helpers.js';
import '../../public/assets/js/config.js';
//window.config = window.config || config;

import DatatableDirective from './directives/datatable';


import $ from 'jquery';
window.$ = window.jQuery = $; // disponibiliza globalmente

import 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';

//import '../../public/assets/js/dashboards-analytics.js';
const appName = 'CRM';


createInertiaApp({
    title: (title) => `${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(DatatableDirective)
            .component('Head', Head)
            .component('Link', Link)
            .mount(el);
    },
    progress: {
        color: '#0868eeff',
    },
});

// Reexecuta scripts do dashboard sempre que mudar de página (ex: após login)
router.on('navigate', async (event) => {
    setTimeout(() => initSidebarScripts(), 300);
    // Espera um pouco para garantir que o DOM da nova página foi montado
    setTimeout(() => initDashboardScripts(), 500);
    setTimeout(() => {reinitSneatMenu();}, 300);
});

window.initMenu = function() {
    if (typeof Menu !== 'undefined') {
        const menu = new Menu(document.getElementById('layout-menu'), {
            orientation: 'vertical',
            closeChildren: true
        });
        window.menuInstance = menu;
        return menu;
    }
};


//para os scripts
window.addEventListener('load', async () => {

    const loadScript = (src) => {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.defer = true;
            script.onload = resolve;
            script.onerror = reject;
            document.body.appendChild(script);
        });
    };

    try {
        await loadScript('/assets/vendor/libs/jquery/jquery.js');
        await loadScript('/assets/vendor/libs/popper/popper.js');
        await loadScript('/assets/vendor/js/bootstrap.js');
        await loadScript('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js');
        await loadScript('/assets/vendor/js/menu.js');
        await loadScript('/assets/vendor/libs/apex-charts/apexcharts.js');
        await loadScript('/assets/js/main.js');




        // executar scripts Sneat ao fim
        initSneatScripts();

    } catch (error) {
        console.error('Sneat scripts load error:', error);
    }
});


function initSidebarScripts() {
  if (typeof $ !== 'undefined' && $('.sidebar-left').length) {
    try {
      if (typeof window.SidebarLarge === 'function') {
        window.SidebarLarge();
      } else {

        // Carregar menu.js
        const script = document.createElement('script');
        script.src = '/assets/vendor/js/menu.js';
        script.defer = true;
        document.body.appendChild(script);

        // Carregar main.js (AGORA CERTO)
        const script2 = document.createElement('script');
        script2.src = '/assets/js/main.js';
        script2.defer = true;
        document.body.appendChild(script2);
      }

      console.log('Sidebar reinicializada com sucesso.');

    } catch (error) {
      console.error('Erro ao reinicializar sidebar:', error);
    }
  }
}

function reinitSneatMenu() {
    // Garante que jQuery existe e que o elemento do menu foi renderizado
    const layoutMenu = document.getElementById('layout-menu');
    if (!layoutMenu) return;

    // Se já houver uma instância, destrói antes
    if (window.menuInstance && typeof window.menuInstance.destroy === 'function') {
        window.menuInstance.destroy();
    }

    // Cria nova instância do menu Sneat
    if (typeof window.initMenu === 'function') {
        window.initMenu();
        console.log("Sneat menu reinicializado após navegação.");
    } else {
        console.warn("initMenu não encontrado!");
    }
}
