<template>
    <button
        type="button"
        :class="buttonClasses"
        @click="handleClick"
    >
        <slot/>
    </button>
</template>

<script>
    export default {
        name: 'PrimaryButton',
        props: {
            disabled: {
                type: Boolean,
                default: false,
            },
            colorClass: {
                type: String,
                default: ''
            }
        },
        computed: {
            buttonClasses() {
                const baseClasses = 'btn-marcasite rounded-md font-bold focus:outline-none focus:ring-2';
                const disabledClasses = this.disabled ? 'opacity-50 cursor-not-allowed' : '';

                return [
                    baseClasses,
                    'px-4 py-2',
                    disabledClasses,
                    this.colorClass
                ].join(' ');
            },
        },
        methods: {
            handleClick(event) {
                if (!this.disabled) {
                this.$emit('click-ev', event);
                }
            },
        },
    };
</script>
