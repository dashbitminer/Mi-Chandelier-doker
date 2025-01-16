<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSheetWeek extends Model
{
    use HasFactory;

    public function timeSheet()
    {
        return $this->belongsTo(TimeSheet::class);
    }

    public function timeSheetProjectWeeks()
    {
        return $this->hasMany(TimeSheetProjectWeek::class);
    }
}
