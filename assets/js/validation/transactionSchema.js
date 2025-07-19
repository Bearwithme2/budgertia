const yup = require('yup');

const transactionSchema = yup.object({
  amount: yup.number().required().positive(),
  description: yup.string().required(),
  date: yup.date().max(new Date(), 'Date cannot be in the future').required(),
  category: yup.number().nullable(),
});

module.exports = { transactionSchema };
