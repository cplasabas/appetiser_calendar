<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventDay extends Model
{
    protected $fillable = ['event_id', 'dow'];

    public function event()
    {
        return $this->belongsTo('App\Event', 'event_id');
    }
}
