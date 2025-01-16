<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'alpha_code',
        'numeric_code',
        'currency_code',
    ];

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    public function countryProjects()
    {
        return $this->hasMany(CountryProject::class);
    }

    public function timeSheetTemplates()
    {
        return $this->hasMany(TimeSheetTemplate::class);
    }
}
