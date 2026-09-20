<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Observers\ProjectObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Override;

#[ObservedBy([ProjectObserver::class])]
class Project extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'link',
        'link_2', //image_path
        'start_date',
        'end_date',
        'completed_at',
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

    public function users() //Relación N:N utilizando tabla pivote para establecer equipos
    {
        return $this->belongsToMany(User::class, 'project_team')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function leader() //Relación N:N utilizando tabla pivote para establecer equipos
    {
        return $this->belongsToMany(User::class, 'project_team')
                    ->wherePivot('role', 'Lider')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function members() //Relación N:N utilizando tabla pivote para establecer equipos
    {
        return $this->belongsToMany(User::class, 'project_team')
                    ->wherePivot('role', 'Miembro')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    //Registra las fechas del proyecto y actualiza los estados antes de que lleguen a la BD
    /* protected static function booted()
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
    } */
    // ! Si el proyecto aún no esta registrado en la BD al crearse tendrá el estatus de pendiente
    #[Override]
    protected static function booted()
    {
        static::saving(function ($project) {
            if (! $project->exists){
                $project->status = 'Pendiente';
            }
        });
    }

    // ! Actualiza el estatus del proyecto a En progreso
    public function markInProgress(): void
    {
        $this->update(['status' => 'En progreso']);
    }

    /* // ! Actualiza el estatus de proyecto a Completado,
    * llena el campo completed_at con la fecha actual al cierre del proyecto
    * distinto a la fecha planeada en el cierre del proyecto
    */
    public function markCompletedAt(): void
    {
        $this->update([
            'status' => 'Completado',
            'completed_at' => now(),
        ]);
    }

    /* // ! Actualiza el estatus del proyecto a Pendiente
    * esto es en caso de que el usuario quiera hacer realizar cambios al proyecto cuando ya se haya puesto en estatus completado
    */
    public function markPending(): void
    {
        $this->update([
            'status' => 'Pendiente',
            'completed_at' => null, // ? el campo regresa a null
        ]);
    }

    public function clients() //Relación entre el cliente y el proyecto
    {
        return $this->belongsToMany(Client::class, 'client_project')
                    ->withTimestamps();
    }

    #[Scope]
    public function MostRecent($query)
    {
        return $query->orderByDesc('created_at');
    }

    #[Scope]
    protected function filter(Builder $query, array $filters): Builder
    {
        return $query
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
// TODO: arreglar todo lo de las fechas y estatus de proyectos ademas de modificar los campos de la migración de proyectos
/* //? los campos a arreglar es el image_path, start_date, end_date, status
* añadir el nuevo campo de completed_at que definirá en que fecha se concluyo el proyecto que estará ligado a una nueva función
* también se debe de arreglar el UI con los nombres de los campos y agregar el nuevo botón con el que se da el proyecto por concluido y
* agregar policy para que solo el manager y talvéz el lider de equipo puedan dar por terminado un proyecto.
*/
