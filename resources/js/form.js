window.axios = require('axios');

window.axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content
};

import Vue from 'vue'

Vue.component('jamboapiForm', require('./layouts/Form.vue').default);

import "@fortawesome/fontawesome-free/css/all.min.css";

import VCalendar from 'v-calendar';
Vue.use(VCalendar, {
	componentPrefix: 'v',
});

import vSelect from 'vue-select'
Vue.component('v-select', vSelect)
import 'vue-select/dist/vue-select.css';

import './directives'
import './filters'
import './plugins/colorpicker'

const form = new Vue({
    el: '#jamboapiForm',
});
