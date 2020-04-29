<template>
    <div ref="dropdown" class="dropdown" :class="{'is-active': active}">
        <div @click="active = !active">
            <slot name="trigger"></slot>
        </div>
        <div class="dropdown_menu" :class="{'is-left': left}">
            <slot name="menu"></slot>
        </div>
    </div>
</template>

<script>

    export default {
        props: {
            left: {
                type: Boolean,
                default: false,
            }
        },

        data() {
            return {
                active: false,
            }
        },

        mounted() {
            window.addEventListener('click', e => {
                if (this.$refs.dropdown.contains(e.target)) {
                    return;
                }

                this.active = false;
            });
        },

        methods: {
            close() {
                this.active = false;
            }
        }
    }
</script>
