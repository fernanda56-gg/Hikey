<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Company extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'address',
        'city',
        'country',
        'phone',
        'web_address',
        'tax_id',
        'company_code',
        'owner_id',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id'); //Relación con el dueño del registro
    }

    public function member() //Relación N:N que devuelve a todos los usuarios
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('role', 'joined_at')
                    ->withTimestamps();
    }

    public static function invitationCode() //Genera el código para unirte a la empresa
    {
        do {
            $code = Str::upper(Str::random(10));
        } while (Company::where('company_code', $code)->exists());

        return $code;
    }

    public function projects() //Relación que retorna los proyectos pertenecientes a la empresa
    {
        return $this->hasMany(Project::class);
    }
}
