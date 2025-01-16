<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSheetProjectWeek extends Model
{
    use HasFactory;

    public function timeSheet()
    {
        return $this->belongsTo(TimeSheet::class);
    }

    public function timeSheetProject()
    {
        return $this->belongsTo(TimeSheetProject::class);
    }

    public function timeSheetWeek()
    {
        return $this->belongsTo(TimeSheetWeek::class);
    }

    public function countryProject()
    {
        return $this->belongsTo(CountryProject::class);
    }
}
