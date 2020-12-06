/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./bootstrap');

// /**
//  * Materialise.css
//  */
// require('materialize-css');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// import SongsList from "Pages/list/SongsList";

Vue.component('songs-list', require('Admin/pages/list/SongsList.vue'));
Vue.component('externals-list', require('Admin/pages/list/ExternalsList.vue'));
Vue.component('authors-list', require('Admin/pages/list/AuthorsList.vue'));
Vue.component('tags-list', require('Admin/pages/list/TagsList.vue'));
Vue.component('songbooks-list', require('Admin/pages/list/SongbooksList.vue'));
Vue.component('news-items-list', require('Admin/pages/list/NewsItemsList.vue'));

Vue.component('author-edit', require('Admin/pages/edit/AuthorEdit.vue'));
Vue.component('external-edit', require('Admin/pages/edit/ExternalEdit.vue'));
Vue.component('tag-edit', require('Admin/pages/edit/TagEdit.vue'));
Vue.component('song-lyric-edit', require('Admin/pages/edit/SongLyricEdit.vue'));
Vue.component('songbook-edit', require('Admin/pages/edit/SongbookEdit.vue'));
Vue.component('news-item-edit', require('Admin/pages/edit/NewsItemEdit.vue'));

Vue.component('user-stats', require('Admin/components/UserStats.vue'));

import { ApolloClient } from 'apollo-client';
import { createUploadLink } from 'apollo-upload-client';
import { InMemoryCache } from 'apollo-cache-inmemory';
import { ApolloLink } from 'apollo-link';

// let web_url = 'http://localhost:8000/graphql';

// if (mix.inProduction()) {
//   web_url = 'https://zpevnik.proscholy.cz/graphql';
// } else {
//   web_url = 'http://localhost:8000/graphql';
// }

var base_url = document.querySelector('#baseUrl').getAttribute('value');
// var user_token = document.querySelector('#userToken').getAttribute('value');
var csrf_token = document.querySelector('meta[name="csrf-token"]').content;

const authMiddleware = new ApolloLink((operation, forward) => {
    // add the authorization to the headers
    operation.setContext({
        headers: {
            'X-CSRF-TOKEN': csrf_token
        }
    });

    return forward(operation);
});

// HTTP connexion to the API
const httpLink = createUploadLink({
    // You should use an absolute URL here
    uri: base_url + '/graphql'
});

// clear the session storage used for caching GrapQL queries on the public frontend
// not doing so causes frontend not updating after admin edits
window.sessionStorage.clear();

const cache = new InMemoryCache();

// Create the apollo client
const apolloClient = new ApolloClient({
    link: authMiddleware.concat(httpLink),
    cache
});

import VueApollo from 'vue-apollo';
Vue.use(VueApollo);

const apolloProvider = new VueApollo({
    defaultClient: apolloClient
});

import Vuetify from 'vuetify';

Vue.use(Vuetify, {
    theme: {
        primary: '#3f51b5',
        secondary: '#00bcd4',
        accent: '#3f51b5',
        error: '#f44336',
        warning: '#ff9800',
        info: '#2196f3',
        success: '#4caf50'
    },
    lang: {
        locales: {
            cs: {
                dataIterator: {
                    rowsPerPageAll: '∞',
                    pageText: '{0}–{1} z {2}',
                    noResultsText:
                        'Nic nebylo nalezeno. Zkuste zkontrolovat vyhledávaný řetězec.'
                },
                dataTable: {
                    rowsPerPageText: 'Počet řádků na stránce:'
                },
                noDataText: 'Data nejsou k dispozici.'
            }
        },
        current: 'cs'
    }
});

import Notifications from 'vue-notification';
Vue.use(Notifications);

import VeeValidate from 'vee-validate';
Vue.use(VeeValidate);

const app = new Vue({
    el: '#app',
    apolloProvider,
    data: {
        dark: false
    }
    // render: h => h(App)
});

window.VueApp = app;
