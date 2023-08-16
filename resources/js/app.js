import Vue from 'vue'
import router from './routes.js'
import store from './store';

import "@fortawesome/fontawesome-free/css/all.min.css";
import "nprogress/nprogress.css";

import Toast from "vue-toastification"
import "vue-toastification/dist/index.css"
const options = {
	draggable: false,
	timeout: 2000,
	pauseOnFocusLoss: false,
	position: "top-center"
}
Vue.use(Toast, options);

Vue.component('app', require('./layouts/App.vue').default);

import Slugify from './plugins/slugify'
Vue.use(Slugify)
import './plugins/colorpicker'

import VCalendar from 'v-calendar';
Vue.use(VCalendar, {
	componentPrefix: 'v',
});

Vue.component('pagination', require('laravel-vue-pagination'));

import vSelect from 'vue-select'
Vue.component('v-select', vSelect)
import 'vue-select/dist/vue-select.css';

import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
const sweetalert2Options = {
	showCancelButton: true,
	confirmButtonColor: '#3085d6',
	cancelButtonColor: '#d33',
	confirmButtonText: 'Yes, I am sure!',
	type: 'warning',
	icon: 'warning',
};
Vue.use(VueSweetalert2, sweetalert2Options);

import './directives'
import './filters'

const app = new Vue({
    el: '#app',
    router,
	store
});
