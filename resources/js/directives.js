import Vue from 'vue'

Vue.directive('forminput', {
    bind: function (el, binding, vnode) {
        el.classList.add('w-full');
        el.classList.add('rounded-sm');
        el.classList.add('border-gray-300');
        el.classList.add('text-sm');
        el.classList.add('p-3');
    }
});
Vue.directive('formcheckbox', {
    bind: function (el, binding, vnode) {
        el.classList.add('focus:ring-indigo-500');
        el.classList.add('h-4');
        el.classList.add('w-4');
        el.classList.add('text-indigo-600');
        el.classList.add('border-gray-300');
        el.classList.add('rounded-sm');
    }
});

Vue.directive('formselect', {
    bind: function (el, binding, vnode) {
        el.classList.add('border');
        el.classList.add('border-gray-300');
        el.classList.add('rounded-sm');
        el.classList.add('text-sm');
        el.classList.add('py-3');
    }
});

Vue.directive('formlabel', {
    bind: function (el, binding, vnode) {
        el.classList.add('block');
        el.classList.add('font-semibold');
        el.classList.add('text-sm');
        el.classList.add('text-gray-900');
        el.classList.add('mb-2');
    }
});