// resources/js/directives/datatable.js
export default {
  install(app) {
    app.directive('datatable', {
      mounted(el, binding) {
        const opts = binding.value || {};
        const pageSizeOptions = opts.pageSizeOptions || [5, 10, 20, 50];
        let pageSize = opts.defaultPageSize || pageSizeOptions[0];
        let currentPage = 1;
        let selectedIds = new Set();

        let idAttr = opts.idAttr || 'data-id';
        const table = el.tagName === 'TABLE' ? el : el.querySelector('table');
        if (!table) return console.warn('v-datatable: table not found inside element');

        // --- wrapper for controls ---
        const wrapper = document.createElement('div');
        wrapper.className = 'd-flex flex-column flex-md-row align-items-start w-100 gap-3 mb-4';
        table.parentNode.insertBefore(wrapper, table);

        const left = document.createElement('div');
        left.className = 'd-flex gap-2 align-items-center w-100';
        wrapper.appendChild(left);

        const select = document.createElement('select');
        select.className = 'form-select form-select-sm';
        select.style.width = 'auto';
        pageSizeOptions.forEach(opt => {
          const o = document.createElement('option');
          o.value = opt;
          o.textContent = opt;
          select.appendChild(o);
        });
        select.value = pageSize;
        left.appendChild(select);

        // WRAPPER COM ÍCONE   imput de pesquisa
        const searchWrapper = document.createElement('div');
        searchWrapper.className = 'position-relative flex-grow-1';

        // ÍCONE
        const searchIcon = document.createElement('i');
        searchIcon.className = 'bx bx-search';
        searchIcon.style.position = 'absolute';
        searchIcon.style.left = '10px';
        searchIcon.style.top = '50%';
        searchIcon.style.transform = 'translateY(-50%)';
        searchIcon.style.fontSize = '18px';
        searchIcon.style.pointerEvents = 'none';
        searchWrapper.appendChild(searchIcon);

        // INPUT
        const input = document.createElement('input');
        input.className = 'form-control form-control-sm ps-5 w-100 ';
        input.placeholder = opts.searchPlaceholder || 'Pesquisar...';
        input.style.maxWidth = opts.searchMaxWidth || '500px';
        searchWrapper.appendChild(input);

        // ADICIONAR AO LADO ESQUERDO
        left.appendChild(searchWrapper);


        const right = document.createElement('div');
        right.className = 'd-flex gap-2 ms-md-auto';
        wrapper.appendChild(right);
        if (opts.actionsHtml) {
          const frag = document.createElement('div');
          frag.innerHTML = opts.actionsHtml;
          Array.from(frag.children).forEach(c => right.appendChild(c));
        }

        const deleteContainer = document.createElement('div');
        deleteContainer.className = 'ms-md-auto';
        deleteContainer.style.display = 'none';
        wrapper.appendChild(deleteContainer);

        const pagination = document.createElement('div');
        pagination.className = 'd-flex justify-content-between align-items-center mt-2';
        table.parentNode.insertBefore(pagination, table.nextSibling);

        // --- helpers ---
        let rowsData = getAllBodyRows();

        function getAllBodyRows() {
          const tb = table.tBodies[0];
          if (!tb) return [];
          return Array.from(tb.querySelectorAll('tr'));
        }

        //funcao para marcar todos elementos da tabela
        function ensureCheckboxes(rows) {
          const headerFirstCell = table.tHead ? table.tHead.rows[0].cells[0] : null;
          if (headerFirstCell && !headerFirstCell.querySelector('input.dt-select-all')) {
            const cb = document.createElement('input');
            cb.type = 'checkbox';
            cb.className = 'form-check-input dt-select-all';
            cb.style.cursor = 'pointer';
            cb.addEventListener('change', (ev) => {
            const checked = ev.target.checked;
            const filteredRows = getFilteredRows(); // usar somente linhas filtradas
            filteredRows.forEach(tr => {
                const rid = tr.getAttribute(idAttr) || tr.dataset.id;
                const rowCb = tr.querySelector('input.dt-row-select');
                if (rowCb) {
                rowCb.checked = checked;
                tr.classList.toggle('table-primary', checked);
                if (checked) selectedIds.add(rid); else selectedIds.delete(rid);
                }
            });
            renderDeleteButton();
            refreshPagination();
            });

            headerFirstCell.insertBefore(cb, headerFirstCell.firstChild);
            headerFirstCell.classList.add("dt-checkbox-cell");
          }

          rows.forEach(tr => {
            const firstCell = tr.cells[0];
            if (!firstCell) return;
            if (!firstCell.querySelector('input.dt-row-select')) {
              const cb = document.createElement('input');
              cb.type = 'checkbox';
              cb.className = 'form-check-input dt-row-select';
              cb.style.cursor = 'pointer';
              cb.addEventListener('change', (ev) => {
                const rid = tr.getAttribute(idAttr) || tr.dataset.id;
                if (ev.target.checked) selectedIds.add(rid); else selectedIds.delete(rid);
                const headerCb = table.querySelector('input.dt-select-all');
                if (headerCb) headerCb.checked = (selectedIds.size === rows.length && rows.length > 0);
                renderDeleteButton();
                tr.classList.toggle('table-primary', ev.target.checked);
              });
              firstCell.insertBefore(cb, firstCell.firstChild);
              firstCell.classList.add("dt-checkbox-cell");
            }
          });
        }


        function renderDeleteButton() {
            if (selectedIds.size > 0) {
                deleteContainer.style.display = 'flex';
                deleteContainer.innerHTML = '';
                const btn = document.createElement('button');
                btn.className = 'btn btn-danger btn-sm';
                btn.textContent = `Eliminar (${selectedIds.size})`;

                btn.addEventListener('click', () => {
                const ids = Array.from(selectedIds);

                if (typeof opts.deleteAction === 'function') {
                    const result = opts.deleteAction(ids);

                    // se a ação retornar uma Promise (Vue/Inertia é async)
                    if (result && typeof result.then === 'function') {
                        result.then(() => resetSelection());
                    } else {
                        resetSelection();
                    }

                } else {
                    table.dispatchEvent(new CustomEvent('datatable-delete', { detail: ids }));
                    resetSelection();
                }

            });

                deleteContainer.appendChild(btn);
                left.style.display = 'none';
                right.style.display = 'none';
            } else {
                deleteContainer.style.display = 'none';
                left.style.display = '';
                right.style.display = '';
            }
        }

        function getFilteredRows() {
          const filter = input.value.trim().toLowerCase();
          return rowsData.filter(tr => !filter || tr.innerText.toLowerCase().includes(filter));
        }

        function resetSelection() {
            selectedIds.clear();

            // desmarcar checkboxes das linhas
            const rowCheckboxes = table.querySelectorAll('input.dt-row-select');
            rowCheckboxes.forEach(cb => {
                cb.checked = false;
                cb.closest('tr').classList.remove('table-primary');
            });

            // desmarcar checkbox "selecionar tudo"
            const selectAll = table.querySelector('input.dt-select-all');
            if (selectAll) selectAll.checked = false;

            renderDeleteButton();
            refreshPagination();
        }


        function refreshPagination() {
          const filtered = getFilteredRows();
          const totalFiltered = filtered.length;
          const totalPages = Math.max(1, Math.ceil(totalFiltered / pageSize));
          if (currentPage > totalPages) currentPage = totalPages;

          const start = (currentPage - 1) * pageSize;
          const end = start + pageSize;

          // Ocultar todas e mostrar apenas as filtradas
          rowsData.forEach(tr => tr.style.display = 'none');
          filtered.forEach((tr, idx) => {
            tr.style.display = idx >= start && idx < end ? '' : 'none';
          });

          // limpar paginação
          pagination.innerHTML = '';

          // info total
          const info = document.createElement('div');
          info.className = "text-muted small";
          const showingStart = totalFiltered === 0 ? 0 : start + 1;
          const showingEnd = Math.min(end, totalFiltered);
          info.textContent = `Mostrando ${showingStart}–${showingEnd} de ${totalFiltered} registos`;
          pagination.appendChild(info);

          // container de páginas
          const pagesContainer = document.createElement('div');
          pagesContainer.className = 'd-flex gap-1';

          // botão anterior
          const prevBtn = document.createElement('button');
          prevBtn.className = 'btn btn-sm btn-outline-secondary';
          prevBtn.textContent = '«';
          prevBtn.disabled = currentPage === 1;
          prevBtn.addEventListener('click', () => { if (currentPage > 1) { currentPage--; refreshPagination(); } });
          pagesContainer.appendChild(prevBtn);

          // páginas numéricas
          for (let p = 1; p <= totalPages; p++) {
            const btn = document.createElement('button');
            btn.className = 'btn btn-sm ' + (p === currentPage ? 'btn-primary text-white' : 'btn-outline-secondary');
            btn.textContent = p;
            btn.addEventListener('click', () => {
              currentPage = p;
              refreshPagination();
            });
            pagesContainer.appendChild(btn);
          }

          // botão próximo
          const nextBtn = document.createElement('button');
          nextBtn.className = 'btn btn-sm btn-outline-secondary';
          nextBtn.textContent = '»';
          nextBtn.disabled = currentPage === totalPages;
          nextBtn.addEventListener('click', () => { if (currentPage < totalPages) { currentPage++; refreshPagination(); } });
          pagesContainer.appendChild(nextBtn);

          pagination.appendChild(pagesContainer);
        }

        // função de ordenação
        function sortRows(colIndex, direction) {
          if (!direction) return refreshPagination();

          rowsData.sort((a, b) => {
            const aText = a.cells[colIndex].innerText.trim().toLowerCase();
            const bText = b.cells[colIndex].innerText.trim().toLowerCase();
            if (aText < bText) return direction === 'asc' ? -1 : 1;
            if (aText > bText) return direction === 'asc' ? 1 : -1;
            return 0;
          });

          const tb = table.tBodies[0];
          rowsData.forEach(tr => tb.appendChild(tr));
          refreshPagination();
        }

        // eventos
        select.addEventListener('change', (e) => {
          pageSize = parseInt(e.target.value, 10);
          currentPage = 1;
          refreshPagination();
        });
        input.addEventListener('input', () => {
          currentPage = 1;
          refreshPagination();
        });

        // observer para v-for
        const tb = table.tBodies[0];

        // setas de ordenacao no cabecalho
        const header = table.tHead ? table.tHead.rows[0] : null;
        if (header) {
          Array.from(header.cells).forEach((th, idx) => {
            if (idx === 0) return; // primeira coluna (checkbox)

            const wrapper = document.createElement('div');
            wrapper.style.display = 'flex';
            wrapper.style.alignItems = 'center';
            wrapper.style.gap = '4px';
            wrapper.style.cursor = 'pointer';

            const label = document.createElement('span');
            label.textContent = th.textContent;
            wrapper.appendChild(label);

            const ascArrow = document.createElement('span');
            ascArrow.textContent = '↑';
            ascArrow.style.fontSize = '0.7em';
            ascArrow.style.opacity = '1';
            wrapper.appendChild(ascArrow);

            const descArrow = document.createElement('span');
            descArrow.textContent = '↓';
            descArrow.style.fontSize = '0.7em';
            descArrow.style.opacity = '1';
            wrapper.appendChild(descArrow);

            th.textContent = '';
            th.appendChild(wrapper);

            let currentSort = null; // null | 'asc' | 'desc'
            wrapper.addEventListener('click', () => {
              currentSort = currentSort === 'asc' ? 'desc' : currentSort === 'desc' ? null : 'asc';
              ascArrow.style.opacity = currentSort === 'asc' ? '2' : '1';
              descArrow.style.opacity = currentSort === 'desc' ? '2' : '1';
              sortRows(idx, currentSort); // idx = índice da coluna
            });
          });
        }

        const mo = new MutationObserver(() => {
          rowsData = getAllBodyRows();
          ensureCheckboxes(rowsData);
          refreshPagination();
        });
        if (tb) mo.observe(tb, { childList: true, subtree: true, characterData: true });

        // inicial
        rowsData = getAllBodyRows();
        ensureCheckboxes(rowsData);
        refreshPagination();

        el.__datatable_cleanup = () => mo.disconnect();
      },
      unmounted(el) {
        if (el.__datatable_cleanup) el.__datatable_cleanup();
      }
    });
  }
}
