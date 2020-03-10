<template>
    <div>
        <div :class="{'mb-4' : size === 'lg'}" class="relative">
            <input :id="id" ref="input" v-bind="$attrs" :class="{ error: errors.length, 'px-5 pt-8 pb-2': size === 'lg', 'px-5 py-3 input-md': size === 'md' }" :type="type" :value="value" @input="$emit('input', $event.target.value)" class="input filled border border-gray-400 appearance-none rounded w-full bg-white focus focus:border-indigo-600 focus:outline-none active:outline-none active:border-indigo-600" />
            <label style="left: 8px;" v-if="label" :for="id" class="label absolute mb-0 -mt-2 pt-4 pl-3 leading-tighter text-gray-600 text-base mt-2 cursor-text">{{ label }}</label>
        </div>

        <div v-if="errors.length" class="form-error">{{ errors[0] }}</div>
    </div>
</template>

<script>
    export default {
        inheritAttrs: false,
        props: {
            id: {
                type: String,
                default() {
                    return `text-input-${this._uid}`;
                }
            },
            size: {
                type: String,
                default: 'lg',
            },
            type: {
                type: String,
                default: 'text'
            },
            value: String,
            label: String,
            errors: {
                type: Array,
                default: () => []
            }
        },
        methods: {
            focus() {
                this.$refs.input.focus();
            },
            select() {
                this.$refs.input.select();
            },
            setSelectionRange( start, end ) {
                this.$refs.input.setSelectionRange( start, end );
            }
        }
    };
</script>
