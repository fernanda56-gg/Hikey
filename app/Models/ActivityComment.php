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
}
