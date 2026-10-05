<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityComments extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'activity_comments';

    protected $fillable = [
        'activity_id',
        'user_id',
        'content'
    ];

    public function activity() // Relación entre Activities y Activity_comments
    {
        return $this->belongsTo(Activity::class);
    }

    public function author() // Relación entre users y comentarios de actividades
    {
        return $this->belongsTo(User::class);
    }
}
