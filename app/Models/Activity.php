<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'activities';

    protected $fillable = [
        'name',
        'description',
        'priority',
        'status',
        'link',
        'due_date',
        'start_date',
        'completed_at',
        'project_id',
        'by_user_id'
    ];

    public function project_activity() // Relación entre un proyecto y sus actividades
    {
        return $this->belongsTo(Project::class);
    }

    public function responsable() // Relación entre la actividad y quien la hizo
    {
        return $this->belongsTo(User::class, 'by_user_id');
    }

    public function user_activity() // Relación tabla pivote para establecer el usuario asignado en la actividad
    {
        return $this->belongsToMany(User::class, 'activity_user')->withTimestamps();
    }

    public function items() // Relación entre los pasos a realizar entre las actividades
    {
        return $this->hasMany(ActivityItem::class)->orderBy('order');
    }
    public function comments() // Relación entre los comentarios y la actividad
    {
        return $this->hasMany(ActivityComments::class)->latest();
    }

    // ! Si la actividad no esta registrada aún en la BD al crearse tendrá estatus disponible
    #[Override]
    protected static function booted()
    {
        static::saving( function ($activity) {
            if (! $activity->exists) {
                $activity->status = 'Disponible';
            }
        });
    }

    // * Funciones para estatus de actividades

    public function markInProgress(): void
    {
        $this->update([
            'status' => 'En progreso',
            'start_date' => now(),
        ]);
    }

    public function sendToReview(): void
    {
        $this->update([
            'status' => 'Revisión'
        ]);
    }

    public function markCompletedAt(): void
    {
        $this->update([
            'status' => 'Completado',
            'completed_at' => now()
        ]);
    }

    // ? si el líder rechaza alguna actividad sus estatus retorna a EN PROGRESO
    public function returnActivity(): void
    {
        $this->update([
            'status' => 'En progreso',
            'completed_at' => null
        ]);
    }
}
