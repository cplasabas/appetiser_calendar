<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

use Carbon\Carbon;

use App\Event;
use App\EventDay;

use Illuminate\Support\Facades\Log;

class CreateEvent
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $event_payload = [
            "name" => $args['name'],
            "period_start" => Carbon::parse($args['period_start'])->format("Y-m-d"),
            "period_end" => Carbon::parse($args['period_end'])->format("Y-m-d")
        ];

        $event_id = Event::create($event_payload)->id;

        foreach ($args['days'] as $key => $day) {
            $day_payload = [
                "event_id" => $event_id,
                "dow" => $day
            ];

            EventDay::create($day_payload);
        }

        return Event::with('days')->find($event_id);
    }
}
