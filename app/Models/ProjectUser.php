<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectUser extends Pivot
{
    use HasFactory;

    protected $table = 'project_users';

    protected $fillable = ['user_id', 'project_id', 'is_leader'];

    public function travelRequests()
    {
        return $this->hasMany(TravelRequest::class);
    }

    // public function user(){
    //     return $this->belongsTo(User::class);
    // }
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
