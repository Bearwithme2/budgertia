<template>
  <div class="dashboard">
    <div class="cards">
      <div class="card">Income: {{ income }}</div>
      <div class="card">Expenses: {{ expenses }}</div>
      <div class="card">Net: {{ net }}</div>
    </div>
    <BudgetBarChart :items="limits" />
    <div class="goals">
      <SavingsGoalRing
        v-for="g in goals"
        :key="g.id"
        :current="g.currentAmount"
        :target="g.targetAmount"
      />
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import BudgetBarChart from '../components/BudgetBarChart.vue';
import SavingsGoalRing from '../components/SavingsGoalRing.vue';
import { useBudgetStore } from '../../stores/budget';
import { useSavingsStore } from '../../stores/savings';
import { useAuthStore } from '../../stores/auth';

const budgetStore = useBudgetStore();
const savingsStore = useSavingsStore();
const authStore = useAuthStore();
const limits = ref([]);
const goals = savingsStore.goals;
const transactions = ref([]);

onMounted(async () => {
  await savingsStore.loadGoals();
  const limitRes = await fetch('/api/budget-check', { headers: { Authorization: `Bearer ${authStore.token}` } });
  if (limitRes.ok) {
    const data = await limitRes.json();
    limits.value = Array.isArray(data.data) ? data.data : [];
  }
  const res = await fetch('/api/transactions', { headers: { Authorization: `Bearer ${authStore.token}` } });
  if (res.ok) {
    const data = await res.json();
    transactions.value = Array.isArray(data.data)
      ? data.data.map(it => ({ amount: it.attributes.amount }))
      : [];
  }
});

const income = computed(() => transactions.value.filter(t => t.amount > 0).reduce((a, b) => a + b.amount, 0));
const expenses = computed(() => transactions.value.filter(t => t.amount < 0).reduce((a, b) => a + Math.abs(b.amount), 0));
const net = computed(() => income.value - expenses.value);
</script>

<style scoped>
.dashboard {
  padding: 1rem;
}
.cards {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}
.card {
  background: #f5f5f5;
  padding: 1rem;
}
.goals {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}
</style>
