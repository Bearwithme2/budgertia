<template>
  <div>
    <header class="header">
      <NotificationBell />
    </header>
    <DashboardView />
  </div>
</template>

<script setup>
import NotificationBell from './components/NotificationBell.vue';
import DashboardView from './views/DashboardView.vue';
</script>

