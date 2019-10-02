<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['name', 'period_start', 'period_end'];

    public function days()
    {
        return $this->hasMany('App\EventDay', 'event_id', 'id');
    }
}
