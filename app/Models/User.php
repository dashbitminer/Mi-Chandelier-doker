<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasRoles;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'country_project_user');
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'country_project_user');
    }

    public function countryProjects()
    {
        return $this->belongsToMany(CountryProject::class, 'country_project_user', 'user_id', 'country_project_id')
            ->withPivot('is_leader')
            ->withTimestamps();
    }

    public function countryProjectUsers()
    {
        return $this->hasMany(CountryProjectUser::class);
    }

    public function timeSheets()
    {
        return $this->hasMany(TimeSheet::class);
    }

    public function travelRequests()
    {
        return $this->hasMany(TravelRequest::class);
    }

    public function timeSheetReviews()
    {
        return $this->hasMany(TimeSheetReview::class);
    }

    public function travelRequestReviews()
    {
        return $this->hasMany(TravelRequestReview::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function userCourses()
    {
        return $this->hasMany(UserCourse::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'user_courses')
            ->withPivot('status', 'end_date');
    }

    public function userTokens()
    {
        return $this->hasMany(UserToken::class);
    }
}
