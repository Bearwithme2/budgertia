const yup = require('yup');

const savingsGoalSchema = yup.object({
  targetAmount: yup.number().required().positive(),
  currentAmount: yup.number().required().min(0),
});

module.exports = { savingsGoalSchema };
