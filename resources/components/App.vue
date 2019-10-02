<template>
  <v-app>
    <v-app-bar
      app
      color="blue darken-4"
      dark
    >
      <v-toolbar-title>Calendar</v-toolbar-title>
    </v-app-bar>

    <v-content>
      <v-container
        class="fill-height"
      >
        <v-layout row wrap v-if="!$apollo.loading">
          <v-flex xs12 md4>
            <v-layout row wrap my-4>
              <v-flex xs12 md12 sm12> 
                <v-text-field label="Name" hint="Event Name" v-model="event_name" clearable/>
              </v-flex>
            </v-layout>
            <v-layout row wrap my-4>
              <v-btn @click="select_date" dark color="blue darken-2">
                <date-range-picker ref="datepicker" style="cursor: pointer" readonly v-model="range" :options="options" format="YYYY-MM-DD"/>
              </v-btn>
            </v-layout>
            <v-layout row wrap my-4>
              <v-flex v-for="(day, key) in days" :key="key">
                <v-checkbox
                style="margin: 0; padding: 0"
                  v-model="days[key].selected"
                  :label="day.text"
                  color="blue darken-2"
                  hide-details
                ></v-checkbox>
              </v-flex>
            </v-layout>
            <v-layout row wrap my-4>
              <v-flex xs12 md12 sm12 class="text-right">
                <v-btn @click="save" dark color="blue darken-2">Save</v-btn>
              </v-flex>
            </v-layout>
          </v-flex>
          <v-flex xs12 md7 offset-md1 v-if="latest_event">
            <v-flex xs12 md12 sm12 class="display-2"> 
              {{period_text}}
            </v-flex>
            <v-list>
              <template v-for="(day, key) in day_range">
                <v-list-item
                  v-bind:class="[has_event(day) ? 'highlight' : '']"
                  :key="key"
                >
                  <v-list-item-content>
                    <v-list-item-title>{{moment(latest_event.period_start).add((day-1), 'days').format("D ddd")}}</v-list-item-title>
                  </v-list-item-content>
                  <v-list-item-content>
                    <v-list-item-title v-if="has_event(day)">{{latest_event.name}}</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
                <v-divider :key="`divider-${key}`"></v-divider>
              </template>
            </v-list>
          </v-flex>
          <v-layout v-else row wrap align-center>
            <v-flex xs12 md12 sm12 class="text-center display-1">
              NO EVENTS
            </v-flex>
          </v-layout>
        </v-layout>
        <v-layout row wrap v-else align-center>
          <v-flex xs12 md12 sm12 class="text-center">
            <v-progress-circular indeterminate color="blue darken-2" :size="75"></v-progress-circular>
          </v-flex>
        </v-layout>
      </v-container>
    </v-content>
    <v-dialog
      v-model="preloader"
      width="300" 
      persistent
    >
      <v-card height="30" style="overflow-y: hidden;">
        <v-card-text>
          <v-container class="fill-height">
            <v-layout row wrap align-center>
              <v-flex xs12 md12 sm12 class="text-center">
                <v-progress-linear indeterminate color="blue darken-2"></v-progress-linear>
              </v-flex>
            </v-layout>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-snackbar
      :timeout="3000"
      bottom
      right
      :color="snackbar.color"
      v-model="snackbar.show"
    >
      {{ snackbar.text }}
      <v-btn dark flat @click.native="snackbar.show = false" icon> 
        <v-icon>close</v-icon>
      </v-btn>
    </v-snackbar> 
    <v-footer
      color="blue darken-4"
      app
    >
      <span class="white--text">&copy; 2019</span>
    </v-footer>
  </v-app>
</template>

<script>
import moment from 'moment'

import { eventsQuery, createEventMutation } from '../js/graphql';

export default {
  props: {
    source: String,
  },
  apollo: {
    events: eventsQuery
  },
  data: () => ({
    moment: moment,
    preloader: false,
    range: [moment().startOf('month').format('YYYY-MM-DD'), moment().endOf('month').format('YYYY-MM-DD')],
    options: {
      startDate: moment().startOf('month').format('YYYY-MM-DD'),
      endDate: moment().endOf('month').format('YYYY-MM-DD')
    },
    events: [],
    event_name: '',
    days: [
      {
        text: "Sun",
        selected: false
      },
      {
        text: "Mon",
        selected: false
      },
      {
        text: "Tue",
        selected: false
      },
      {
        text: "Wed",
        selected: false
      },
      {
        text: "Thu",
        selected: false
      },
      {
        text: "Fri",
        selected: false
      }
    ],
    snackbar: {
      show: false,
      text: '',
      color: '',
    }
  }),    
  computed: {
    start_date: function () {
      return this.options.startDate;
    },
    period_text: function () {
      let text = "";

      if (this.latest_event) {
        text = `${moment(this.latest_event.period_start).format("MMM YYYY")} - ${moment(this.latest_event.period_end).format("MMM YYYY")}`;

        if (moment(this.latest_event.period_start).isSame(moment(this.latest_event.period_end), 'month')) {
          text = moment(this.latest_event.period_start).format("MMM YYYY")
        }
      }
      return text;
    },
    latest_event: function () {
      let event = null;
      
      if (this.events.length !== 0) {
        return this.events[this.events.length-1];
      }
      return event;
    },
    day_range: function () {
      if (this.latest_event) {
        let period_start = moment(this.latest_event.period_start);
        let period_end = moment(this.latest_event.period_end);

        return period_end.diff(period_start, 'days') + 1
      }
      
      return 0
    },
  },
  methods: {
    select_date () {
      this.$refs.datepicker.$el.click();
    },
    has_event (day) {
      let dow = moment(this.latest_event.period_start).add((day-1), 'days').day();

      return this.latest_event.days.some(day => day.dow === dow);
    },
    save () {
      this.preloader = true;
      let days = [];

      this.days.filter((day, key) => {
        
        if (day.selected) {
          days.push(key);
        }

        return true;
      })

      let payload = {
        name: this.event_name,
        period_start: this.range[0],
        period_end: this.range[1],
        days: days
      }

      this.$apollo.mutate({
        mutation: createEventMutation,
        variables: payload,

        update: (store, { data: { createEvent } }) => {
          
          const data = store.readQuery({ query: eventsQuery })

          data.events.push(createEvent)

          store.writeQuery({ query: eventsQuery, data })
        },
      }).then((data) => {
        this.preloader = false;

        this.snackbar = {
          show: true,
          text: 'Event Successfully Added',
          color: 'blue darken-2',
        }
      }).catch((error) => {
        this.preloader = false;

        this.snackbar = {
          show: true,
          text: 'Error',
          color: 'red darken-4',
        }
      })
    }
  },

};
</script>


<style scoped>
  .highlight {
      background-color: #BBDEFB;
  }
</style>
