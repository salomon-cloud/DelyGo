<template>
  <div>
    <h2>Productos</h2>
    <ul>
      <li v-for="producto in productos" :key="producto.id">
        {{ producto.nombre }} - ${{ producto.precio }}
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref } from 'vue';
const productos = ref([
  { id: 1, nombre: 'Pizza', precio: 120 },
  { id: 2, nombre: 'Sushi', precio: 90 },
]);
</script>
