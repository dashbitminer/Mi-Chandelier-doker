<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CountryProjectUser extends Pivot
{
    use HasFactory;

    protected $table = 'country_project_user';

    protected $fillable = [
        'country_project_id',
        'user_id',
        'is_leader',
    ];

    public function countryProject()
    {
        return $this->belongsTo(CountryProject::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function travelRequests()
    {
        return $this->hasMany(TravelRequest::class);
    }
}
