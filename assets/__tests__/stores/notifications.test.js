const { setActivePinia, createPinia } = require('pinia');
const { useNotificationStore } = require('../../stores/notifications');
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

test('fetchList updates items', async () => {
  const auth = useAuthStore();
  auth.token = 't';
  global.fetch.mockResolvedValueOnce({
    ok: true,
    json: () => Promise.resolve({ data: [{ id: '1', attributes: { message: 'x', level: 'info', createdAt: '', isRead: false } }] })
  });
  const store = useNotificationStore();
  await store.fetchList();
  expect(store.items).toHaveLength(1);
});

test('connectStream falls back to polling', () => {
  const auth = useAuthStore();
  auth.token = 't';
  const store = useNotificationStore();
  jest.useFakeTimers();
  const spySet = jest.spyOn(global, 'setInterval');
  global.EventSource = undefined;
  store.connectStream();
  expect(spySet).toHaveBeenCalled();
  jest.advanceTimersByTime(30000);
  expect(spySet).toHaveBeenCalled();
  store.disconnect();
  spySet.mockRestore();
  jest.useRealTimers();
});

