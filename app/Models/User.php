<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'company_id',
        'profile_photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'profile_photo_url'
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

    public function projects() //Relación 1:N con el usuario que genero el proyecto
    {
        return $this->hasMany(Project::class, 'by_user_id');
    }


    public function project_team() //Relación N:N usando tabla pivote para relación entre usuario y proyecto
    {
        return $this->belongsToMany(Project::class, 'project_team')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function companyOwner() //Relación 1:N con el usuario dueño y la empresa creada
    {
        return $this->hasMany(Company::class, 'owner_id');
    }

    public function companies() //Relación N:N usando tabla pivote donde se incluye a los miembros(user) y el propietario de la empresa
    {
        return $this->belongsToMany(Company::class)
                    ->withPivot('role', 'joined_at')
                    ->withTimestamps();
    }

    public function isOwner(Company $company) //Comprueba si el usuario es el dueño de la empresa
    {
        return $this->id === $company->owner_id;
    }

    public function isAdmin():bool
    {
        return $this->hasRole('admin');
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo
            ? Storage::disk('public')->url($this->profile_photo)
            : null;
    }

    #[Scope]
    public function MostRecent($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    #[Scope]
    protected function filter(Builder $query, array $filters): Builder
    {
        $query
            ->when(
                $filters['role'] ?? false,
                fn($query, $value) => $query->whereHas('roles', function ($query) use ($value) {
                    $query->where('name', $value);
                })
            )->when(
                $filters['name'] ?? false,
                fn($query, $value) => $query->where('name', 'like', "%$value%")
            );
        return $query;
    }
}
