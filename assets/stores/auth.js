const { defineStore } = require('pinia');
const { ref } = require('vue');
const { buildOptions } = require('../utils/request');

const storage = typeof localStorage !== 'undefined' ? localStorage : {
  getItem: () => '',
  setItem: () => {},
  removeItem: () => {},
};

exports.useAuthStore = defineStore('auth', () => {
  const token = ref(storage.getItem('token') || '');

  async function login(email, password) {
    const res = await fetch('/api/login', buildOptions('POST', '', JSON.stringify({ email, password })));
    if (!res.ok) {
      throw new Error('Login failed');
    }
    const data = await res.json();
    token.value = data.token;
    storage.setItem('token', token.value);
  }

  async function register(email, password) {
    const res = await fetch('/api/register', buildOptions('POST', '', JSON.stringify({ email, password })));
    if (!res.ok) {
      throw new Error('Registration failed');
    }
    const data = await res.json();
    token.value = data.token;
    storage.setItem('token', token.value);
  }

  async function refresh() {
    if (!token.value) {
      return;
    }
    const res = await fetch('/api/token/refresh', buildOptions('POST', token.value));
    if (res.ok) {
      const data = await res.json();
      token.value = data.token;
      storage.setItem('token', token.value);
    }
  }

  function logout() {
    token.value = '';
    storage.removeItem('token');
  }

  return { token, login, register, refresh, logout };
});

