<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    protected $fillable = ['start_time', 'end_time'];

    public function formattedTime()
    {
        $start = date('H:i', strtotime($this->start_time));
        $end = $this->end_time === '00:00:00'
            ? '24:00'
            : date('H:i', strtotime($this->end_time));

        return $start . ' - ' . $end;
    }
}
