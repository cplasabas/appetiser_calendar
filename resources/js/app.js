import Vue from 'vue';
import App from '../components/App';

import moment from 'vue-moment'

Vue.use(moment);

import VueApollo from 'vue-apollo'

Vue.use(VueApollo)

import ApolloClient from 'apollo-boost'

const apolloClient = new ApolloClient({
  uri: 'http://localhost:8012/graphql'
})

const apolloProvider = new VueApollo({
  defaultClient: apolloClient,
})

import Vuetify from 'vuetify'

Vue.use(Vuetify)

import DateRangePicker from "@gravitano/vue-date-range-picker";

Vue.use(DateRangePicker);

new Vue({
  el: '#app',
  apolloProvider,
  vuetify: new Vuetify({}),
  components: { App },
  template: '<App/>'
});