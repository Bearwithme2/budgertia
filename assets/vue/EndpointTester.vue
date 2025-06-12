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
    <p
      v-if="error"
      class="error"
    >
      {{ error }}
    </p>
    <div v-if="status !== null">
      <h2>Status: {{ status }}</h2>
      <pre>{{ response }}</pre>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { buildOptions, isValidJson } from '../utils/request';

const token = ref('');
const method = ref('GET');
const endpoint = ref('');
const body = ref('');
const status = ref(null);
const response = ref('');
const error = ref('');

const methods = ['GET', 'POST', 'PUT', 'DELETE'];

function send() {
  if (!endpoint.value) {
    error.value = 'Endpoint is required';
    return;
  }
  if (['POST', 'PUT'].includes(method.value) && body.value && !isValidJson(body.value)) {
    error.value = 'Body must be valid JSON';
    return;
  }

  status.value = null;
  response.value = '';
  error.value = '';
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
.error {
  color: #dc3545;
  margin-top: 1em;
}
</style>

