<template>
  <svg :width="size" :height="size" :viewBox="`0 0 ${size} ${size}`">
    <circle
      :cx="center"
      :cy="center"
      :r="radius"
      stroke="#eee"
      :stroke-width="stroke"
      fill="none"
    />
    <circle
      :cx="center"
      :cy="center"
      :r="radius"
      stroke="#4caf50"
      :stroke-width="stroke"
      fill="none"
      stroke-linecap="round"
      :stroke-dasharray="circumference"
      :stroke-dashoffset="offset"
      :transform="`rotate(-90 ${center} ${center})`"
    />
    <text
      x="50%"
      y="50%"
      text-anchor="middle"
      dy=".3em"
    >{{ percent }}%</text>
  </svg>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  current: Number,
  target: Number,
  size: { type: Number, default: 80 },
  stroke: { type: Number, default: 8 }
});

const center = computed(() => props.size / 2);
const radius = computed(() => props.size / 2 - props.stroke / 2);
const circumference = computed(() => 2 * Math.PI * radius.value);
const percent = computed(() => Math.round((props.current / props.target) * 100));
const offset = computed(() => circumference.value * (1 - props.current / props.target));
</script>
