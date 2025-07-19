const yup = require('yup');

const budgetLimitSchema = yup.object({
  amount: yup.number().required().positive(),
  category: yup.number().required().positive(),
});

module.exports = { budgetLimitSchema };
