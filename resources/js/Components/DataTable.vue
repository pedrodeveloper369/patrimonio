<script setup>
import DataTable from "@/Components/DataTable.vue";
import { ref } from "vue";

const rows = ref([
  { id: 1, name: "João", email: "joao@example.com", role: "Admin", status: "Active" },
]);

const columns = [
  { label: "Name", key: "name" },
  { label: "Email", key: "email" },
  { label: "Role", key: "role" },
  { label: "Status", key: "status" },
];
z
function handleDelete(ids) {
  console.log("Eliminar IDs:", ids);
  // chamada para API
}
</script>

<template>

</template>
