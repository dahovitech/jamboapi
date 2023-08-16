export default {
    install(Vue){
        Vue.prototype.$slugify = function(text, ampersand = 'and'){
            const a = 'àáäâèéëêìíïîòóöôùúüûñçßÿỳýœæŕśńṕẃǵǹḿǘẍźḧğüşöçı'
            const b = 'aaaaeeeeiiiioooouuuuncsyyyoarsnpwgnmuxzhgusoci'
            const p = new RegExp(a.split('').join('|'), 'g')
    
            return text.toString().toLowerCase()
                .replace(/[\s_]+/g, '-')
                .replace(p, c =>
                b.charAt(a.indexOf(c)))
                .replace(/&/g, `-${ampersand}-`)
                .replace(/[^\w-]+/g, '')
                .replace(/--+/g, '-')
            .replace(/^-+|-+$/g, '');
        }
    }
}