import Vue from 'vue';
import Vuex from 'vuex';
import VueRouter from 'vue-router';

import Layout from './Components/Layout.vue';
import Routes from './Routes';
import Store from './Store';

/**
 * Plugins
 */
Vue.use(VueRouter);

/**
 * Components
 */
Vue.component( "layout", Layout );

new Vue({
    el: '#location-domination-app',
    router: Routes,
    store: Store,
});