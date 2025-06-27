module.exports = {
    env: {
        browser: true,
        es2021: true,
        node: true,
        jquery: true
    },
    extends: [
        'eslint:recommended',
        'prettier'
    ],
    parserOptions: {
        ecmaVersion: 12,
        sourceType: 'module'
    },
    globals: {
        '$': 'readonly',
        'jQuery': 'readonly',
        'bootstrap': 'readonly',
        'moment': 'readonly',
        'Swal': 'readonly',
        'toastr': 'readonly'
    },
    rules: {
        'indent': ['error', 4],
        'linebreak-style': ['error', 'unix'],
        'quotes': ['error', 'single'],
        'semi': ['error', 'always'],
        'no-unused-vars': ['warn'],
        'no-console': ['warn'],
        'no-debugger': ['error'],
        'prefer-const': ['error'],
        'no-var': ['error']
    }
};
