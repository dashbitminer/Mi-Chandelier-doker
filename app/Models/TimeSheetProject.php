<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSheetProject extends Model
{
    use HasFactory;

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function timeSheet()
    {
        return $this->belongsTo(TimeSheet::class);
    }

    public function timeSheetProjectTimes()
    {
        return $this->hasMany(TimeSheetProjectTime::class);
    }

    public function timeSheetProjectWeeks()
    {
        return $this->hasMany(TimeSheetProjectWeek::class);
    }

    public function countryProject()
    {
        return $this->belongsTo(CountryProject::class);
    }
}
