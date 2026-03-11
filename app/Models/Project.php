<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'link',
        'image_path',
        'start_date',
        'end_date',
        'status',
        'by_user_id',
        'area_id',
        'company_id',
        'client_id',
    ];

    public function project_owner() //Relación con el usuario que genero el proyecto
    {
        return $this->belongsTo(User::class, 'by_user_id');
    }

    public function company() //Relación con la empresa ala que el proyecto pertenece
    {
        return $this->belongsTo(Company::class);
    }

    public function area() //Relación con el área del proyecto
    {
        return $this->belongsTo(Area::class);
    }

    protected static function booted() //Registra las fechas del proyecto y actualiza los estados antes de que lleguen a la BD
    {
        static::saving(function ($project) {
            if($project->end_date){
                $project->status = 'Completado';
            } elseif($project->start_date){
                $project->status = 'En progreso';
            } else{
                $project->status = 'Pendiente';
            }
        });
    }

    public function clients() //Relación entre el cliente y el proyecto
    {
        /* return $this->belongsToMany(Client::class); */
        return $this->belongsToMany(Client::class, 'client_project')
                    ->withTimestamps();
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
                $filters['status'] ?? false, //Evalúa la condición si existe el valor o no
                fn($query, $value) => $query->where('status', $value)
                /* El query actúa como la consulta y el $value es el valor que se pasa del filtro */
            )->when(
                $filters['name'] ?? false,
                fn($query, $value) => $query->where('name', 'like', "%$value%")
            )->when(
                $filters['area'] ?? false,
                fn($query, $value) => $query->where('area_id', $value)
            );
    }


}
