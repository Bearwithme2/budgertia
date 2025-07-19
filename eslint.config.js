module.exports = [
  {
    files: ['assets/**/*.js'],
    ignores: ['public/**', 'vendor/**', 'node_modules/**'],
    languageOptions: { ecmaVersion: 2021, sourceType: 'module' },
    rules: { 'no-unused-vars': 'error' },
  },
  {
    files: ['assets/**/*.vue'],
    ignores: ['public/**', 'vendor/**', 'node_modules/**'],
    languageOptions: {
      parser: require('vue-eslint-parser'),
      parserOptions: {
        parser: '@babel/eslint-parser',
        ecmaVersion: 2021,
        sourceType: 'module',
        requireConfigFile: false,
      },
    },
    plugins: { vue: require('eslint-plugin-vue') },
    rules: { ...require('eslint-plugin-vue').configs['vue3-recommended'].rules },
  },
];
