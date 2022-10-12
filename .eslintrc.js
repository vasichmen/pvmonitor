// eslint-disable-next-line no-undef
module.exports = {
    extends: [
        'eslint:recommended',
        'plugin:vue/recommended',
    ],
    rules: {
        'vue/no-v-html': ['off'],
        'vue/no-template-shadow': ['off'],
        'vue/html-indent': ['error', 2],
        'vue/script-indent': ['error', 2, {
            'baseIndent': 1,
            'switchCase': 1,
        }],
        'vue/component-definition-name-casing': ['error', 'kebab-case'],
        'vue/html-self-closing': [
            'error', {
                html: {
                    normal: 'never',
                    void: 'always',
                },
            },
        ],
        'semi': ['error', 'always'],
        'quotes': ['error', 'single', { 'allowTemplateLiterals': true }],
        'comma-dangle': ['error', 'always-multiline'],
        'object-curly-spacing': ['warn', 'always'],
        'keyword-spacing': 'warn',
        'no-multi-spaces': 'warn',
    },
    overrides: [
        {
            'files': ['*.vue'],
            'rules': {
                'indent': 'off',
                'no-case-declarations':'off',
            },
        },
    ],
};
