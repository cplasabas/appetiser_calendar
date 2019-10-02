<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public function days()
    {
        return $this->hasMany('App\EventDay', 'event_id', 'id');
    }
}
