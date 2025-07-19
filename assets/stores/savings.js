const { defineStore } = require('pinia');
const { ref } = require('vue');
const { buildOptions } = require('../utils/request');
const { useAuthStore } = require('./auth');

exports.useSavingsStore = defineStore('savings', () => {
  const auth = useAuthStore();
  const goals = ref([]);

  async function loadGoals() {
    const res = await fetch('/api/savings-goals', buildOptions('GET', auth.token));
    if (res.ok) {
      const data = await res.json();
      goals.value = Array.isArray(data.data)
        ? data.data.map(it => ({
            id: parseInt(it.id, 10),
            targetAmount: it.attributes.targetAmount,
            currentAmount: it.attributes.currentAmount,
          }))
        : [];
    }
  }

  return { goals, loadGoals };
});
