<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'project_id',
        'company_id',
        'client_id',
    ];

    public function company() //Relación con la empresa
    {
        return $this->belongsTo(Company::class);
    }

    public function projects() //Relación con el proyecto y el cliente
    {
        /* return $this->belongsToMany(Project::class); */
        return $this->belongsToMany(Project::class, 'client_project')
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
                $filters['name'] ?? false, //Evalúa la condición si existe el valor o no
                fn($query, $value) => $query->where('name', 'like', "%$value%")
            )->when(
                $filters['projectName'] ?? false,
                fn($query, $value) => $query->whereHas('projects', function($query) use ($value){
                    $query->where('name', 'like', "%$value%");
                })
            )->when(
                $filters['companyName'] ?? false,
                fn($query, $value) => $query->whereHas('company', function($query) use ($value){
                    $query->where('name', 'like', "%$value%");
                })
            );
    }
}
