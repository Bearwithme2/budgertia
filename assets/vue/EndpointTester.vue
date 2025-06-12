<template>
  <div>
    <h1>API Tester</h1>
    <div>
      <label>Token
        <input
          v-model="token"
          placeholder="JWT token"
        >
      </label>
    </div>
    <div>
      <label>Method
        <select v-model="method">
          <option
            v-for="m in methods"
            :key="m"
            :value="m"
          >{{ m }}</option>
        </select>
      </label>
    </div>
    <div>
      <label>Endpoint
        <input
          v-model="endpoint"
          placeholder="/api/categories"
        >
      </label>
    </div>
    <div>
      <label>Body</label>
      <textarea v-model="body" />
    </div>
    <button @click="send">
      Send
    </button>
    <div v-if="status !== null">
      <h2>Status: {{ status }}</h2>
      <pre>{{ response }}</pre>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { buildOptions } from '../utils/request';

const token = ref('');
const method = ref('GET');
const endpoint = ref('');
const body = ref('');
const status = ref(null);
const response = ref('');

const methods = ['GET', 'POST', 'PUT', 'DELETE'];

function send() {
  status.value = null;
  response.value = '';
  const options = buildOptions(method.value, token.value, body.value);
  fetch(endpoint.value, options)
    .then(async res => {
      status.value = res.status;
      response.value = await res.text();
    })
    .catch(err => {
      status.value = 0;
      response.value = String(err);
    });
}
</script>

<style scoped>
textarea {
  width: 100%;
  height: 150px;
}
pre {
  background: #f8f9fa;
  padding: 1em;
  white-space: pre-wrap;
  word-break: break-all;
}
</style>

