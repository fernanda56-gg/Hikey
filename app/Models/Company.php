<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

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
        } while (DB::table('companies')->where('company_code', $code)->exists());

        return $code;
    }

    public function projects() //Relación que retorna los proyectos pertenecientes a la empresa
    {
        return $this->hasMany(Project::class);
    }

    public function clients() //Relación que registra a que empresa pertenece el cliente
    {
        return $this->hasMany(Client::class);
    }

    #[Scope]
    public function MostRecent($query)
    {
        return $query->orderByDesc('created_at');
    }

    #[Scope]
    protected function filter(Builder $query, array $filters): void
    {
        $query
            ->when(
                $filters['city'] ?? false, //Evalúa la condición si existe el valor o no
                fn($query, $value) => $query->where('city', 'like', "%$value%")
                /* El query actúa como la consulta y el $value es el valor que se pasa del filtro */
            )->when(
                $filters['name'] ?? false,
                fn($query, $value) => $query->where('name', 'like', "%$value%")
            )->when(
                $filters['country'] ?? false,
                fn($query, $value) => $query->where('country', 'like', "%$value%")
            );
    }
}
