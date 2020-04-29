const mix = require('laravel-mix');
const glob = require('glob-all');

require('dotenv').config();
require('laravel-mix-purgecss');
require('laravel-mix-tailwind');
require('laravel-mix-alias');

mix.alias({
    '@': '/resources/js',
});

mix.browserSync({
    proxy: process.env.APP_URL,
    ui: false,
});

mix.options({
    processCssUrls: false,
});

mix
    .js('resources/js/app.js', 'public/js')
    .js('resources/js/landing.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .tailwind()
    .purgeCss({
        enabled: mix.inProduction(),
        extensions: ['php', 'vue'],
        folders: ['./resources/views', './resources/js'],
        paths: glob.sync([
            path.join(__dirname, './resources/views/**/*.blade.php'),
            path.join(__dirname, './resources/js/**/*.vue'),
        ]),
        defaultExtractor: content => content.match(/[\w-/:]+(?<!:)/g) || [],
        whitelistPatterns: [/^(is|js|os)/],
    })
    .version()
;
