import gql from 'graphql-tag';


export const eventsQuery = gql`
  query {
    events{
      id
      name
      period_start
      period_end
      days{
        dow
      }
    }
  }`;

export const createEventMutation = gql`
  mutation createEvent($name: String! $period_end: Date! $period_start: Date! $days: [Int!]!) {
    createEvent(name: $name, period_end: $period_end, period_start: $period_start, days: $days){
      id
      name
      period_start
      period_end
      days{
        dow
      }    
    }
  }`;