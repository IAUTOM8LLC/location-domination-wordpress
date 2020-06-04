import Vue from 'vue';
import Vuex from 'vuex';
import VueRouter from 'vue-router';

import Layout from './Components/Layout.vue';
import PostBuilder from './Components/PostBuilder.vue';
import Routes from './Routes';
import Store from './Store';
import VueToastr from 'vue-toastr';

/**
 * Plugins
 */
Vue.use(VueRouter);
Vue.use(VueToastr);

/**
 * Components
 */
Vue.component( "layout", Layout );
Vue.component( "post-builder", PostBuilder );

new Vue({
    el: '#location-domination-app',
    router: Routes,
    store: Store,
});

new Vue({
   el: '#location-domination-settings',
});