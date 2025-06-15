let setActivePinia;
let createPinia;
let useAuthStore;

beforeEach(() => {
  jest.resetModules();
  ({ setActivePinia, createPinia } = require('pinia'));
  setActivePinia(createPinia());
  global.localStorage = {
    getItem: jest.fn(() => ''),
    setItem: jest.fn(),
    removeItem: jest.fn(),
  };
  global.fetch = jest.fn(() => Promise.resolve({
    ok: true,
    json: () => Promise.resolve({ token: 'abc' })
  }));
  useAuthStore = require('../../stores/auth').useAuthStore;
});

test('login stores token', async () => {
  const store = useAuthStore();
  await store.login('e@example.com', 'x');
  expect(store.token).toBe('abc');
  expect(global.localStorage.setItem).toHaveBeenCalled();
});

test('logout clears token', () => {
  const store = useAuthStore();
  store.logout();
  expect(store.token).toBe('');
});

