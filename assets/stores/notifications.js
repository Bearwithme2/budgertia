const { defineStore } = require('pinia');
const { ref, computed } = require('vue');
const { buildOptions } = require('../utils/request');
const { useAuthStore } = require('./auth');

exports.useNotificationStore = defineStore('notifications', () => {
  const auth = useAuthStore();
  const items = ref([]);
  let source = null;
  let poll = 0;

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

  function startPolling() {
    fetchList();
    poll = setInterval(fetchList, 30000);
  }

  function connectStream() {
    if (source || poll) {
      return;
    }
    if (typeof EventSource !== 'undefined') {
      source = new EventSource(`/api/notifications/stream?token=${auth.token}`);
      source.onmessage = (e) => {
        try {
          const data = JSON.parse(e.data);
          items.value.unshift({ id: parseInt(e.lastEventId, 10), ...data });
        } catch {
          // ignore
        }
      };
      source.onerror = () => {
        disconnect();
        startPolling();
      };
    } else {
      startPolling();
    }
  }

  function disconnect() {
    if (source) {
      source.close();
      source = null;
    }
    if (poll) {
      clearInterval(poll);
      poll = 0;
    }
  }

  const unread = computed(() => items.value.filter(n => !n.isRead).length);

  return { items, unread, fetchList, markRead, markAllRead, connectStream, disconnect };
});

