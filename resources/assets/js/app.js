window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('chord', require('Public/pages/Song/Chord.vue'));
Vue.component('song-part-tag', require('Public/pages/Song/SongPartTag.vue'));
Vue.component('external-view', require('Public/components/ExternalView.vue'));
Vue.component(
    'dark-mode-button',
    require('Public/components/DarkModeButton.vue')
);
Vue.component('search', require('Public/pages/Search/Search.vue'));
Vue.component('song-view', require('Public/pages/Song/SongView.vue'));
Vue.component('recaptcha', require('Public/pages/Login/Recaptcha.vue'));

Vue.component(
    'user-account',
    require('Public/pages/UserAccount/UserAccount.vue')
);

// firebase firestore plugin for vue
// import { firestorePlugin } from 'vuefire'
// Vue.use(firestorePlugin)

import { ApolloClient } from 'apollo-client';
import { createHttpLink } from 'apollo-link-http';
import { InMemoryCache } from 'apollo-cache-inmemory';
import { ApolloLink } from 'apollo-link';
import VueApollo from 'vue-apollo';

var base_url = document.querySelector('#baseUrl').getAttribute('value');
var csrf_token = document.querySelector('meta[name="csrf-token"]').content;

// HTTP connexion to the API
const httpLink = createHttpLink({
    // You should use an absolute URL here
    uri: base_url + '/graphql'
});

const cache = new InMemoryCache();

const authMiddleware = new ApolloLink((operation, forward) => {
    // add the authorization to the headers
    operation.setContext({
        headers: {
            'X-CSRF-TOKEN': csrf_token
        }
    });

    return forward(operation);
});

// Create the apollo client
const apolloClient = new ApolloClient({
    link: authMiddleware.concat(httpLink),
    cache
});

Vue.use(VueApollo);

const apolloProvider = new VueApollo({
    defaultClient: apolloClient
});

Vue.directive('auth', function(el, binding, vnode) {
    if (user_token == '') {
        el.style.display = 'none';
    }
});

const app = new Vue({
    el: '#app',
    apolloProvider
});
