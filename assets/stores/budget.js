const { defineStore } = require('pinia');
const { ref } = require('vue');
const { buildOptions } = require('../utils/request');
const { useAuthStore } = require('./auth');

exports.useBudgetStore = defineStore('budget', () => {
  const auth = useAuthStore();
  const limits = ref([]);

  async function loadLimits() {
    const res = await fetch('/api/budget-limits', buildOptions('GET', auth.token));
    if (res.ok) {
      const data = await res.json();
      limits.value = Array.isArray(data.data)
        ? data.data.map(it => ({
            id: parseInt(it.id, 10),
            amount: it.attributes.amount,
            category: it.attributes.category,
          }))
        : [];
    }
  }

  async function addLimit(amount, category) {
    const body = JSON.stringify({ amount, category });
    const res = await fetch('/api/budget-limits', buildOptions('POST', auth.token, body));
    if (!res.ok) {
      throw new Error('Create failed');
    }
    const data = await res.json();
    limits.value.push({
      id: parseInt(data.data.id, 10),
      amount: data.data.attributes.amount,
      category: data.data.attributes.category,
    });
  }

  async function updateLimit(id, payload) {
    const res = await fetch(`/api/budget-limits/${id}`, buildOptions('PUT', auth.token, JSON.stringify(payload)));
    if (!res.ok) {
      throw new Error('Update failed');
    }
    const data = await res.json();
    const idx = limits.value.findIndex(l => l.id === id);
    if (idx !== -1) {
      limits.value[idx] = {
        id: parseInt(data.data.id, 10),
        amount: data.data.attributes.amount,
        category: data.data.attributes.category,
      };
    }
  }

  async function deleteLimit(id) {
    const res = await fetch(`/api/budget-limits/${id}`, buildOptions('DELETE', auth.token));
    if (!res.ok) {
      throw new Error('Delete failed');
    }
    limits.value = limits.value.filter(l => l.id !== id);
  }

  return { limits, loadLimits, addLimit, updateLimit, deleteLimit };
});

