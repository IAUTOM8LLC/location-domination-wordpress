<template>
    <div>
        <div :class="{'mb-4' : size === 'lg'}" class="relative">
            <div  class="input filled border border-gray-600 appearance-none rounded w-full bg-white focus focus:border-indigo-600 focus:outline-none active:outline-none active:border-indigo-600" :class="{ error: errors.length, 'px-5 pt-8 pb-2': size === 'lg', 'px-5 py-3 input-md': size === 'md' }">
                <multiselect v-on="listeners" v-bind="$attrs" ref="input" :id="id" v-model="selected" :label="labelKey" :options="options" :show-labels="false"></multiselect>
            </div>

            <label style="top:0;left:8px;" v-if="label" :for="id" class="label absolute mb-0 -mt-2 pt-4 pl-3 leading-tighter text-gray-600 text-base mt-2 cursor-text">{{ label }}</label>
        </div>

        <div v-if="errors.length" class="form-error">{{ errors[0] }}</div>
    </div>
</template>

<style>
    .multiselect__tags {
        border: 0 !important;
        padding: 8px 22px 0 0 !important;
    }

    .multiselect__placeholder {
        display: none !important;
    }

    .multiselect__tag {
        padding: 8px 27px 8px 15px !important;
        border-radius: 0px !important;
        font-weight: 400 !important;
        color: #0a1541 !important;
        border: 1px solid #dae1e7 !important;
        background: #fff !important;
        font-size: 12px !important;
    }

    .multiselect__tag-icon:focus, .multiselect__tag-icon:hover {
        background: #fff !important;
    }

    .multiselect__tag-icon {
        line-height: 30px !important;
    }
</style>

<script>
    import Multiselect from 'vue-multiselect';

    export default {
        inheritAttrs: false,
        components: {
            Multiselect
        },
        props: {
            labelKey: {
                type: String,
                default: 'name',
            },
            id: {
                type: String,
                default() {
                    return `advancedselect-input-${this._uid}`;
                }
            },
            size: {
                type: String,
                default: 'lg'
            },
            options: {
                type: Array,
                default: []
            },
            value: [String, Number, Boolean, Array, Object],
            label: String,
            errors: {
                type: Array,
                default: () => []
            }
        },
        data() {
            return {
                selected: []
            };
        },
        computed: {
            listeners() {
                return {
                    ...this.$listeners,
                    input: value => this.$emit( 'input', value )
                }
            }
        },
        watch: {
            selected( selected ) {
                this.$emit( 'input', selected );
            }
        },

        mounted() {
          if(this.value) {
              this.selected = this.value;
          }
        },

        methods: {
            focus() {
                this.$refs.input.focus();
            },
            select() {
                this.$refs.input.select();
            }
        }
    };
</script>
