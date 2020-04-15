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
    return iziToast[type]({title, message, timeout, position: 'bottomLeft'})
}

Vue.use(VueNotifications, {
    success: toast,
    error: toast,
    info: toast,
    warn: toast
})

// Export VueJS
window.Vue = Vue;
