/**
 * Bootstrapping the app
 */

require('./bootstrap');

// Register utils
// --
require('./vue');

/**
* Registering vue components
 */

const files = require.context('./components', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

/**
 * Creating vue app
 */

const app = new Vue({
    el: '#app',
});

import Dropdown from "./utils/Dropdown";
new Dropdown('.js-dropdown');
