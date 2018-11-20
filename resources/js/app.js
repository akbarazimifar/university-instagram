window.client_id = 2;
window.client_secret = 'M9LOZCLDZQB7uliwDoNYmRDSJ0PwrRtBKzVulRtw';
window.Vue = require('vue');
import VueRouter from 'vue-router';
import Vuex from 'vuex';
Vue.use(Vuex);
Vue.use(VueRouter);
Vue.use(require('vue-axios'), require('axios'));
Vue.axios.defaults.baseURL = 'http://localhost:3000/';
Vue.router = VueRouter;
Vue.use(require("muse-ui"));
Vue.router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'default',
            component: require('./components/home.vue')
        }, {
            path: '/login',
            name: 'login',
            component: require('./components/login.vue'),
            meta: {
                auth: false,
            }
        }, {
            path: '/register',
            name: 'register',
            component: require('./components/register.vue'),
            meta: {
                auth: false,
            }
        }, {
            path: '/home',
            name: 'home',
            component: require('./components/dashboard.vue'),
            meta: {
                auth: true,
            }
        }, {
            path: '/404',
            name: 'notFound',
            component: require('./components/404.vue')
        }, {
            path: '*',
            name: 'toNotFound',
            redirect: '/404'
        }
    ]
});


Vue.use(require('@websanova/vue-auth'), {
    auth: {
        request: function (req, token) {
            var refresh = req.url.indexOf('token') > -1;
            if (refresh) {
                if (req["grant_type"] === "password")
                    refresh = false;
            }
            token = token.split(';')
            if (!refresh) {
                token = token[0]
                req.headers['Authorization'] = 'Bearer ' + token
            } else {
                token = token[1]
                req.data = {
                    'refresh_token': token,
                    'grant_type': 'refresh_token',
                    client_id: window.client_id,
                    client_secret: window.client_secret,
                }
            }
        },
        response: function (res) {
            if (res.data.access_token && res.data.refresh_token) {
                {
                    return res.data.access_token + ';' + res.data.refresh_token
                }
            }
        }
    },
    http: require('@websanova/vue-auth/drivers/http/axios.1.x.js'),
    router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js'),
    //rolesVar: 'permissions',
    loginData: {url: '/oauth/token', method: 'POST', redirect: '/home', fetchUser: false},
    fetchData: {enabled: false},
    //logoutData: {url: '/api/user/logout', method: 'POST', redirect: '/login', makeRequest: true},
    registerData: {url: '/api/register', method: 'POST',redirect:false},
    refreshData: {url: '/oauth/token/refresh ', method: 'POST', enabled: false, interval: 15},
    forbiddenRedirect: { path: '/404' },
    notFoundRedirect: { path: '/404' },
    tokenStore: ['localStorage', 'cookie'],
});
window.$ = function(peyload){
    return(document.querySelector(peyload));
}
var component = require('./components/routManager.vue');
component.router = Vue.router;
var el = new Vue(component).$mount("#App");

