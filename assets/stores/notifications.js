const { defineStore } = require('pinia');
const { ref, computed } = require('vue');
const { buildOptions } = require('../utils/request');
const { useAuthStore } = require('./auth');

exports.useNotificationStore = defineStore('notifications', () => {
  const auth = useAuthStore();
  const items = ref([]);

  async function fetchList(params = '') {
    const q = params ? `?${params}` : '';
    const res = await fetch(`/api/notifications${q}`, buildOptions('GET', auth.token));
    if (res.ok) {
      const data = await res.json();
      items.value = Array.isArray(data.data)
        ? data.data.map(it => ({ id: parseInt(it.id, 10), ...it.attributes }))
        : [];
    }
  }

  async function markRead(id) {
    const res = await fetch(`/api/notifications/${id}/read`, buildOptions('PATCH', auth.token));
    if (res.ok) {
      await fetchList();
    }
  }

  async function markAllRead() {
    const res = await fetch('/api/notifications/read-all', buildOptions('PATCH', auth.token));
    if (res.ok) {
      items.value = items.value.map(n => ({ ...n, isRead: true }));
    }
  }

  const unread = computed(() => items.value.filter(n => !n.isRead).length);

  return { items, unread, fetchList, markRead, markAllRead };
});

