<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSheetTemplateHoliday extends Model
{
    use HasFactory;

    public function timeSheetTemplate()
    {
        return $this->belongsTo(TimeSheetTemplate::class);
    }
}
