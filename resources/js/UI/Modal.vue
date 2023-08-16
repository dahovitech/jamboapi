<template>
    <div class="fixed z-10 inset-0 overflow-y-auto" v-show="show">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center  sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="close">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-sm text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle w-full" :class="maxWidthClass" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                
                <div v-if="!disableHeader" class="px-6 py-6 border-b border-gray-100 bg-white">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        <slot name="title">
                        </slot>
                    </h3>
                </div>

                <div :class="{'px-6 py-4': !noContenPadding}">
                    <slot name="content">
                    </slot>
                </div>
                
                <div v-if="!disableFooter" class="px-6 py-4 bg-gray-50 text-right mt-5">
                    <slot name="footer">
                    </slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            show: {
                default: false
            },
            maxWidth: {
                default: '3xl'
            },
            closeable: {
                default: true
            },
            disableHeader: {
                default: false
            },
            disableFooter: {
                default: false
            },
            noContenPadding:{
                default: false
            } 
        },

        methods: {
            close() {
                if (this.closeable) {
                    this.$emit('close')
                }
            }
        },

        watch: {
            show: {
                immediate: true,
                handler: (show) => {
                    if (show) {
                        document.body.style.overflow = 'hidden'
                    } else {
                        document.body.style.overflow = null
                    }
                }
            }
        },

        created() {
            const closeOnEscape = (e) => {
                if (e.key === 'Escape' && this.show) {
                    this.close()
                }
            }

            document.addEventListener('keydown', closeOnEscape)

            this.$once('hook:destroyed', () => {
                document.removeEventListener('keydown', closeOnEscape)
            })
        },

        computed: {
            maxWidthClass() {
                return {
                    'sm': 'sm:max-w-sm',
                    'md': 'sm:max-w-md',
                    'lg': 'sm:max-w-lg',
                    'xl': 'sm:max-w-xl',
                    '2xl': 'sm:max-w-2xl',
                    '3xl': 'sm:max-w-3xl',
                    '4xl': 'sm:max-w-4xl',
                    '5xl': 'sm:max-w-5xl',
                    '6xl': 'sm:max-w-6xl',
                    '7xl': 'sm:max-w-7xl',
                }[this.maxWidth]
            }
        }
    }
</script>
