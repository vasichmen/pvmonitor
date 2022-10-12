/**
 * Это фейковый файл для того, чтобы IDE подхватывала alias путей
 * См. webpack.mix.js
 */
const path = require('path');

module.exports = {
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js/'),
        },
    },
};
