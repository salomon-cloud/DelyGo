<template>
  <div>
    <h2>Órdenes</h2>
    <ul>
      <li v-for="orden in ordenes" :key="orden.id">
        #{{ orden.id }} - Estado: {{ orden.estado }} - Restaurante: {{ orden.restaurante.nombre }}
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref } from 'vue';
const ordenes = ref([
  { id: 1, estado: 'recibida', restaurante: { nombre: 'Pizza Palace' } },
  { id: 2, estado: 'preparando', restaurante: { nombre: 'Sushi House' } },
]);
</script>
