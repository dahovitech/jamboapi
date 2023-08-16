<template>
    <div class="relative" ref="dropdown">
        <div @click="open = ! open">
            <slot name="trigger"></slot>
        </div>

        <transition
            enter-active-class="transition ease-out duration-200"
            enter-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95">
            <div v-show="open"
                    class="absolute z-50 rounded-sm shadow-lg"
                    :class="[widthClass, alignmentClasses]"
                    style="display: none;"
                    @click="close">
                <div class="rounded-sm shadow-xs" :class="contentClasses">
                    <slot name="content"></slot>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
    export default {
        props: {
            align: {
                default: 'right'
            },
            width: {
                default: '48'
            },
            contentClasses: {
                default: () => ['bg-white']
            },
            closeable: {
                default: true
            }
        },

        data() {
            return {
                open: false
            }
        },

        methods: {
            close(){
                if(this.closeable)
                    this.open = !this.open
            }
        },

        created() {
            const closeOnEscape = (e) => {
                if (this.open && e.keyCode === 27) {
                    this.open = false
                }
            }
            this.$once('hook:destroyed', () => {
                document.removeEventListener('keydown', closeOnEscape)
            })
            document.addEventListener('keydown', closeOnEscape)

            const documentClick = (e) => {
                try {
                    let el = this.$refs.dropdown;
                    let target = e.target;
                    if (el !== target && !el.contains(target)) {
                        this.open = false
                    }
                } catch (error) {}
            };

            document.addEventListener("click", documentClick);
        },

        computed: {
            widthClass() {
                return {
                    '48': 'w-48',
                    '60': 'w-60',
                    '64': 'w-64',
                }[this.width.toString()]
            },

            alignmentClasses() {
                if (this.align == 'left') {
                    return 'origin-top-left left-0'
                } else if (this.align == 'right') {
                    return 'origin-top-right right-0'
                } else {
                    return 'origin-top'
                }
            },
        }
    }
</script>
