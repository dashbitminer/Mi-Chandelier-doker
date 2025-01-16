<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSheet extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function timeSheetProjects()
    {
        return $this->hasMany(TimeSheetProject::class);
    }

    public function timeSheetProjectTimes()
    {
        return $this->hasMany(TimeSheetProjectTime::class);
    }

    public function timeSheetReviews()
    {
        return $this->hasMany(TimeSheetReview::class);
    }

    public function timeSheetWeeks()
    {
        return $this->hasMany(TimeSheetWeek::class);
    }

    public function timeSheetProjectWeeks()
    {
        return $this->hasMany(TimeSheetProjectWeek::class);
    }

    public function timeSheetTemplate()
    {
        return $this->belongsTo(TimeSheetTemplate::class);
    }
}
