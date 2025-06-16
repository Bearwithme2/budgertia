<template>
  <div class="notification-bell" @click="toggle">
    <span class="icon">🔔</span>
    <span v-if="unread > 0" class="badge">{{ unread }}</span>
    <NotificationDropdown
      v-if="open"
      :notifications="items"
      @mark-all-read="handleMarkAll"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import NotificationDropdown from './NotificationDropdown.vue';
import { useNotificationStore } from '../../stores/notifications';

const store = useNotificationStore();
const { items, unread, fetchList, markAllRead, connectStream, disconnect } = store;

const open = ref(false);

function toggle() {
  open.value = !open.value;
}

function handleMarkAll() {
  markAllRead();
}

onMounted(() => {
  connectStream();
  fetchList();
});

onUnmounted(() => {
  disconnect();
});
</script>

<style scoped>
.notification-bell {
  position: relative;
  cursor: pointer;
  display: inline-block;
}
.icon {
  font-size: 1.5rem;
}
.badge {
  position: absolute;
  top: 0;
  right: 0;
  background: red;
  color: #fff;
  border-radius: 50%;
  padding: 0 4px;
  font-size: 0.75rem;
}
</style>
