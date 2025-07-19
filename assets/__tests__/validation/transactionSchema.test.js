const { transactionSchema } = require('../../js/validation/transactionSchema');

test('valid transaction passes', async () => {
  const data = {
    amount: 5,
    description: 'foo',
    date: '2000-01-01',
    category: 1,
  };
  await expect(transactionSchema.isValid(data)).resolves.toBe(true);
});

test('future date fails', async () => {
  const data = {
    amount: 5,
    description: 'foo',
    date: '2999-01-01',
  };
  await expect(transactionSchema.isValid(data)).resolves.toBe(false);
});
