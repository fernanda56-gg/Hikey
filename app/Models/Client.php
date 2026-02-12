<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //
    use HasFactory;

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
}
