import Vuex from 'vuex';
import Vue from 'vue';

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    AccountConnected: false,
  },

  mutations: {
    setAccountConnected(state, payload) {
      state.AccountConnected = payload;
    },
  },

  getters: {
    isAccountConnected(state) {
      return state.AccountConnected;
    }
  }
});