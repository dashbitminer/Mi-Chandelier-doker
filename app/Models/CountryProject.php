<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'country_id',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'country_project_user', 'country_project_id', 'user_id')
            ->withPivot('is_leader')
            ->withTimestamps();
    }
}
