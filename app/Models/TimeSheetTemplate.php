<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSheetTemplate extends Model
{
    use HasFactory;

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function timeSheetTemplateHolidays()
    {
        return $this->hasMany(TimeSheetTemplateHoliday::class);
    }

    public function timeSheets()
    {
        return $this->hasMany(TimeSheet::class);
    }
}
