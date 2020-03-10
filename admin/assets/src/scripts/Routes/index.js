import VueRouter from "vue-router";
import Dashboard from "../Pages/Dashboard.vue";
import Account from "../Pages/Account.vue";

const routes = [
    { path: '/', redirect: '/dashboard' },
    { path: '/dashboard', component: Dashboard, name: 'dashboard.index' },
    { path: '/account', component: Account, name: 'account.index' }
];

export default new VueRouter({
    routes
});