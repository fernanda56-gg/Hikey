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
    ];

    public function project_owner()
    {
        return $this->belongsTo(User::class, 'by_user_id');
    }
}
