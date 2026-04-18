module.exports = [
    {
        files: ['**/*.js'],
        ignores: [
            'node_modules/**',
            'vendor/**',
            'build/**',
            'dist/**',
            'coverage/**',
            '**/*.min.js',
        ],
        languageOptions: {
            ecmaVersion: 2022,
            sourceType: 'script',
            globals: {
                module: 'readonly',
                window: 'readonly',
                document: 'readonly',
                console: 'readonly',
                FormData: 'readonly',
                fetch: 'readonly',
                setTimeout: 'readonly',
                clearTimeout: 'readonly',
                URLSearchParams: 'readonly',
                URL: 'readonly',
                HTMLElement: 'readonly',
                Event: 'readonly',
                CustomEvent: 'readonly',
            },
        },
        rules: {
            'no-unused-vars': ['warn', { argsIgnorePattern: '^_' }],
            'no-undef': 'error',
            'semi': ['error', 'always'],
            'quotes': ['error', 'single'],
        },
    },
];