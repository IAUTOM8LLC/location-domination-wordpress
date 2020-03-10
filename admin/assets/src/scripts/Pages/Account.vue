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
import { mapGetters, mapMutations } from 'vuex';
import axios from 'axios';
import TextInput from '../Components/Inputs/TextInput.vue';
import LdButton from '../Components/LdButton.vue';

  export default {
    name: "Account",

    components: { LdButton, TextInput },

    computed: {
      ...mapGetters([ 'isAccountConnected' ]),
    },

    data() {
      return {
        form: {
          apiKey: ''
        }
      };
    },

    methods: {
      ...mapMutations([ 'setAccountConnected' ]),

      connect() {
        axios.get(`https://locationdomination.net/api/website/${this.form.apiKey}/`)
            .then(({ data }) => {
                axios.post(this.$parent.ajaxUrl, {
                  _nonce: this.$parent.nonce,
                  action: 'ld_update_apikey',
                  apiKey: this.form.apiKey,
                })
                this.setAccountConnected(true);
            });
      }
    },
  }
</script>

<style scoped>

</style>