<template>
  <div class="dropdown">
    <ul class="list">
      <li
        v-for="n in notifications"
        :key="n.id"
        :class="n.level"
        class="item"
      >
        <span class="msg">{{ n.message }}</span>
        <small class="time">{{ formatDate(n.createdAt) }}</small>
      </li>
    </ul>
    <button
      v-if="unread > 0"
      type="button"
      @click="markAllRead"
      class="mark-all"
    >Mark all read</button>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  notifications: {
    type: Array,
    default: () => [],
  },
});
const emit = defineEmits(['mark-all-read']);

function markAllRead() {
  emit('mark-all-read');
}

function formatDate(d) {
  return new Date(d).toLocaleString();
}

const unread = computed(() => props.notifications.filter(n => !n.isRead).length);
</script>

<style scoped>
.dropdown {
  position: absolute;
  right: 0;
  background: #fff;
  border: 1px solid #ccc;
  padding: 0.5rem;
  min-width: 200px;
}
.list {
  list-style: none;
  margin: 0;
  padding: 0;
  max-height: 200px;
  overflow-y: auto;
}
.item {
  display: flex;
  justify-content: space-between;
  padding: 2px 0;
}
.msg {
  margin-right: 0.5rem;
}
.mark-all {
  margin-top: 0.5rem;
}
</style>
