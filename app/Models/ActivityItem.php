<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'activity_items';

    protected $fillable = [
        'activity_id',
        'description',
        'is_completed',
        'order',
    ];

    protected $casts = [
        'is_completed' => 'boolean'
    ];

    public function activity() // Relación entre Activities y Activity_items
    {
        return $this->belongsTo(Activity::class);
    }
}
