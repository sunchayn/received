import Vue from "vue";
import VueNotifications from 'vue-notifications'
import iziToast from 'izitoast'
import 'izitoast/dist/css/iziToast.min.css'
import Fragment from 'vue-fragment';

// Configuration
Vue.config.productionTip = false;

// VueJS Fragments
Vue.use(Fragment.Plugin);

// VueJS Notifications
function toast ({title, message, type, timeout, cb}) {
    if (type === VueNotifications.types.warn) type = 'warning';
    return iziToast[type]({title, message, timeout, position: 'bottomCenter'})
}

Vue.use(VueNotifications, {
    success: toast,
    error: toast,
    info: toast,
    warn: toast
});

// Vue Filters
Vue.filter('capitalize', function (value) {
    if (!value) return '';
    value = value.toString();
    return value.charAt(0).toUpperCase() + value.slice(1);
});

// Export VueJS
window.Vue = Vue;
