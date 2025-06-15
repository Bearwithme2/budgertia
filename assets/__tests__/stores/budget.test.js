const { setActivePinia, createPinia } = require('pinia');
const { useBudgetStore } = require('../../stores/budget');
const { useAuthStore } = require('../../stores/auth');

beforeEach(() => {
  setActivePinia(createPinia());
  global.fetch = jest.fn(() => Promise.resolve({
    ok: true,
    json: () => Promise.resolve({ data: [] })
  }));
  global.localStorage = {
    getItem: jest.fn(() => ''),
    setItem: jest.fn(),
    removeItem: jest.fn(),
  };
});

test('loadLimits populates list', async () => {
  const auth = useAuthStore();
  auth.token = 't';
  global.fetch.mockResolvedValueOnce({
    ok: true,
    json: () => Promise.resolve({ data: [{ id: '1', attributes: { amount: 5, category: 2 } }] })
  });
  const store = useBudgetStore();
  await store.loadLimits();
  expect(store.limits).toHaveLength(1);
});

