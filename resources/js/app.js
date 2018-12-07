window.client_id = 2;
window.client_secret = 'G8OQhWqLj2CC9V1Wn8YbXwdCZNJAsODIDuM8QNMo'; // Nariman To Az In Estefade kon :| Hamin line o uncomment kon
//window.client_secret = 'ekcBhRGW7JIfSMFxZ6Tz1lVAw9gXIzNTC90EGFrO'; // Manam az in Estefade mikonam :v 
window.Vue = require('vue');
import VueRouter from 'vue-router';
import Vuex from 'vuex';
Vue.use(Vuex);
Vue.use(VueRouter);
Vue.use(require('vue-axios'), require('axios'));
Vue.axios.defaults.baseURL = 'http://localhost:8000/';
Vue.router = VueRouter;
Vue.use(require("muse-ui"));
Vue.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');



Vue.router = new VueRouter({
    mode: 'history',
    routes: [{
        path: '/',
        name: 'default',
        component: require('./components/dashboard.vue'),
        meta: {
            auth: true,
        },
        children: [{
            path: '/feeds',
            name: 'feeds',
            component: require('./components/pages/timeline.vue')
        }, {
            path: '/search',
            name: 'search',
            component: require('./components/pages/search.vue')
        }, {
            path: '/u/:username',
            name: 'profile',
            component: require('./components/pages/profile.vue')
        }, {
            path: '/upload',
            name: 'upload',
            component: require('./components/pages/upload.vue')
        }
        
        ]
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
        path: '/404',
        name: 'notFound',
        component: require('./components/404.vue')
    }, {
        path: '*',
        name: 'toNotFound',
        redirect: '/404'
    }]
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
                req.headers['Accept'] = 'application/json'
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
    parseUserData: function (data) {
        return data;
    },
    http: require('@websanova/vue-auth/drivers/http/axios.1.x.js'),
    router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js'),
    loginData: {
        url: '/oauth/token',
        method: 'POST',
        redirect: '/home',
        fetchUser: true
    },
    fetchData: {url: '/api/user/self', method: 'GET', enabled: true},
    logoutData: {url: '/api/user/logout', method: 'POST', redirect: '/login', makeRequest: true},
    registerData: {
        url: '/api/register',
        method: 'POST'
    },
    refreshData: {
        url: '/oauth/token',
        method: 'POST',
        enabled: true,
        interval: 15
    },
    forbiddenRedirect: {
        path: '/404'
    },
    notFoundRedirect: {
        path: '/404'
    },
    tokenStore: ['localStorage', 'cookie'],
});
window.$ = function (peyload) {
    return (document.querySelector(peyload));
}
var component = require('./components/routManager.vue');



Vue.mixin({
    methods: {
        follow: function (username) {
            return Vue.axios.patch("/api/" + username + "/follow")
            .then(function(data){
                return data.data
            }
            ).catch(function(data){
                return data.response.data
            });
        },
        unFollow: function (username) {
          return Vue.axios.patch("/api/" + username + "/unfollow")
            .then(function(data){
              return data.data
            })
            .catch(function(data) {
              return data.response.data;
            })
        },
        like: function (username,id) {
            return Vue.axios.patch("api/" + username + "/media/" + id + "/like")
            .then(function(data){
                return data.data
            }
            ).catch(function(data){
                return data.response.data
            });
        },
        unLike: function (username,id) {
            return Vue.axios.patch("api/" + username + "/media/" + id + "/disslike")
            .then(function(data){
                return data.data
            }
            ).catch(function(data){
                return data.response.data
            });
        }
    }
});


component.router = Vue.router;
var _app = new Vue({
    render: h => h(component)
}).$mount("#App");
