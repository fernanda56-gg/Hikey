<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

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
}
