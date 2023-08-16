import Vue from 'vue'

Vue.filter('prettyBytes', function (num, kib=1000) {
    if (typeof num !== 'number' || isNaN(num)) {
        throw new TypeError('Expected a number');
    }
    var exponent;
    var unit;
    var neg = num < 0;
    var units = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

    if (neg) {
        num = -num;
    }

    if (num < 1) {
        return (neg ? '-' : '') + num + ' B';
    }

    exponent = Math.min(Math.floor(Math.log(num) / Math.log(kib)), units.length - 1);
    num = (num / Math.pow(kib, exponent)).toFixed(2) * 1;
    unit = units[exponent];

    return (neg ? '-' : '') + num + ' ' + unit;
});

import moment from 'moment';
Vue.filter('date', function(value, arg=null) {
    if (value) {
        let format = 'YYYY-MM-DD';

        if(arg !== null)
            format = arg

        return moment(String(value)).format(format)
    }
});

Vue.filter('truncate', function(text, length, clamp){
    clamp = clamp || '...';
    var node = document.createElement('div');
    node.innerHTML = text;
    var content = node.textContent;
    return content.length > length ? content.slice(0, length) + clamp : content;
});
