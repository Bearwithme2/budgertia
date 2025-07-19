const { setActivePinia, createPinia } = require('pinia');
const { useSavingsStore } = require('../../stores/savings');
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

test('loadGoals populates list', async () => {
  const auth = useAuthStore();
  auth.token = 't';
  global.fetch.mockResolvedValueOnce({
    ok: true,
    json: () => Promise.resolve({ data: [{ id: '1', attributes: { targetAmount: 100, currentAmount: 20 } }] })
  });
  const store = useSavingsStore();
  await store.loadGoals();
  expect(store.goals).toHaveLength(1);
});
