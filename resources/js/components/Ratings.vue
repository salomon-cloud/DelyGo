<template>
  <div>
    <h2>Calificaciones</h2>
    <ul>
      <li v-for="rating in ratings" :key="rating.id">
        Orden #{{ rating.orden_id }} - {{ rating.puntuacion }} estrellas
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref } from 'vue';
const ratings = ref([
  { id: 1, orden_id: 1, puntuacion: 5 },
  { id: 2, orden_id: 2, puntuacion: 4 },
]);
</script>
