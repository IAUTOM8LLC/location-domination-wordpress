<template>
    <div>
        <h3 class="text-dark font-bold">Location Domination Integration</h3>
        <p class="text-grey font-medium mt-5 text-md">
            To make sure you are accessing all of Location Domination's features, you must first update your API key.
        </p>

        <form @submit.prevent="connect">
            <text-input class="mt-8" v-model="form.apiKey" label="API Key" />
            <ld-button type="submit" class="mt-8">Connect my account</ld-button>
        </form>
    </div>
</template>

<script>
    import {mapGetters, mapMutations} from 'vuex';
    import axios from 'axios';
    import {stringify} from 'qs';
    import TextInput from '../Components/Inputs/TextInput.vue';
    import LdButton from '../Components/LdButton.vue';
    import {ExternalRepository} from '../Repositories/ExternalRepository';

    export default {
        name: 'Account',

        components: { LdButton, TextInput },

        computed: {
            ...mapGetters( ['isAccountConnected'] )
        },

        data() {
            return {
                form: {
                    apiKey: ''
                }
            };
        },
        mounted() {
            axios.get( `${this.$parent.ajaxUrl}?action=location_domination_get_settings` ).then( ( { data } ) => {
                this.form = data;
                this.setAccountConnected( data.connected );
            } );
        },

        methods: {
            ...mapMutations( ['setAccountConnected'] ),

            connect() {
                const BASE_URL = ExternalRepository.getBaseUrl();
                axios.get( `${BASE_URL}/api/website/${this.form.apiKey}/` )
                     .then( ( { data } ) => {
                         axios.post( `${this.$parent.ajaxUrl}?action=location_domination_update_settings`, stringify( {
                             _nonce: this.$parent.nonce,
                             apiKey: this.form.apiKey
                         } ) ).then( ( { data } ) => {
                             console.log( data );
                         } );

                         this.setAccountConnected( true );
                     } ).catch( ( error ) => {
                    this.$toastr.Add( {
                        name: 'LDIntegration', // this is give you ability to use removeByName method
                        title: 'Incorrect API Key', // Toast Title
                        msg: 'It looks like we were not able to find your API key. Please make sure it is correct.', // Toast Message
                        clickClose: false, // Click Close Disable
                        timeout: 10000, // Remember defaultTimeout is 5 sec.(5000) in this case the toast won't close automatically
                        //progressBarValue: 50, // Manually update progress bar value later; null (not 0) is default
                        position: 'toast-bottom-center', // Toast Position.
                        type: 'error', // Toast type,
                        preventDuplicates: true //Default is false,
                    } );
                } );
            }
        }
    };
</script>

<style scoped>

</style>