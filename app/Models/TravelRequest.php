<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_project_user_id',
        'departure_date',
        'arrival_date',
        'description',
        'status',
        'request_cash_advance',
        'created_at',
    ];

    public function expenses()
    {
        return $this->hasMany(TravelRequestExpense::class);
    }

    public function countryProjectUser()
    {
        return $this->belongsTo(CountryProjectUser::class, 'country_project_user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function travelRequestReviews()
    {
        return $this->hasMany(TravelRequestReview::class);
    }
}
