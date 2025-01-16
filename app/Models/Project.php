<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'country_project_user');
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class)
            ->withTimestamps();
    }
}
