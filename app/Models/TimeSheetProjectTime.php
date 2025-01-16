<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSheetProjectTime extends Model
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

    public function timeSheetProject()
    {
        return $this->belongsTo(TimeSheetProject::class);
    }

    public function absence_type()
    {
        return $this->belongsTo(AbsenceType::class);
    }
}
