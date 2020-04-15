const mix = require('laravel-mix');
const glob = require('glob-all');

require('dotenv').config()
require('laravel-mix-purgecss');
require('laravel-mix-tailwind');
require('laravel-mix-alias');

mix.alias({
    '@': '/resources/js',
});

mix.browserSync({
    proxy: process.env.APP_URL,
});

mix.options({
    processCssUrls: false,
});

mix
    .js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .tailwind()
    .purgeCss({
        enabled: mix.inProduction(),
        extensions: ['php'],
        folders: ['src/resources/views'],
        paths: glob.sync([
            path.join(__dirname, 'src/resources/views/**/*.blade.php'),
        ]),
        defaultExtractor: content => content.match(/[\w-/:]+(?<!:)/g) || [],
        whitelistPatterns: [/^(is|js|os)/],
    })
    .version()
;
