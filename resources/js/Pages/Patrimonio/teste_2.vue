 <!-- <BaseTable
                :rows="users"
                :columns="columns"
                @delete-selected="handleDelete"
                @row-action="handleRowAction"
            >
                <!-- BOTÕES À DIREITA -->
                <template #actions>
                    <button class="btn btn-outline-secondary btn-sm">
                        Exportar
                    </button>

                    <button class="btn btn-primary btn-sm" style="min-width: 90px;">
                        + Adicionar
                    </button>
                </template>

                <!-- BOTÃO ELIMINAR PERSONALIZADO -->
                <template #delete-action="{ count, ids }">
                    <button
                        class="btn btn-danger btn-sm"
                        @click="openDeleteModal(ids)"
                    >
                        Eliminar ({{ count }})
                    </button>
                </template>

                <!--personalizacao dos dados da tabela-->
                <!-- CUSTOM STATUS BADGE -->
                <template #col-status="{ value }">
                    <span
                        class="px-2 py-1 text-xs font-semibold rounded"
                        :class="{
                            'bg-green-100 text-green-700': value === 'Active',
                            'bg-red-100 text-red-700': value === 'Inactive',
                            'bg-yellow-100 text-yellow-700': value === 'Pending',
                        }"
                    >
                        {{ value }}
                    </span>
                </template>
             </BaseTable>-->
