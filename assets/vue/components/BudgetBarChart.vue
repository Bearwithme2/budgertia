<template>
  <Bar :data="chartData" :options="options" />
</template>

<script setup>
import { computed } from 'vue';
import { Bar } from 'vue-chartjs';
import {
  Chart,
  BarElement,
  CategoryScale,
  LinearScale,
  Tooltip,
  Legend
} from 'chart.js';

Chart.register(BarElement, CategoryScale, LinearScale, Tooltip, Legend);

const props = defineProps({
  items: {
    type: Array,
    default: () => []
  }
});

const chartData = computed(() => ({
  labels: props.items.map(i => `Cat ${i.category}`),
  datasets: [
    {
      label: 'Spent',
      backgroundColor: '#36a2eb',
      data: props.items.map(i => i.spent)
    },
    {
      label: 'Limit',
      backgroundColor: '#ff6384',
      data: props.items.map(i => i.limit)
    }
  ]
}));

const options = {
  responsive: true,
  plugins: { legend: { position: 'bottom' } }
};
</script>
