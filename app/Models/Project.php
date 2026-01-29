<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    //
    use HasFactory;

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
}
