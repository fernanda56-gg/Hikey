<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Activity;
use App\Models\ActivityItem;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Override;

class ActivityItemsImport implements ToModel, WithHeadingRow, WithValidation
{
    #[Override]
    public function model(array $row): ?Model
    {
        // ? Modificación de formato de fechas
        $created_at = !empty($row['created_at'])
        ? Carbon::parse($row['created_at'])->format('Y-m-d H:i:s')
        : now();

        // ? se obtiene la actividad
        $activity = Activity::where('name', $row['activity_name'])->first();

        // ! se genera el registro
        $item = ActivityItem::create([
            'activity_id' => $activity->id,
            'description' => $row['description'],
            'is_completed' => $row['is_completed'],
            'order' => $row['order'],
            'created_at' => $created_at,
            'updated_at' => null
        ]);
        $item->save();

        return null;
    }

    #[Override]
    public function rules(): array
    {
        return [
            
            'is_completed' => 'required',
            'order' => 'required'
        ];
    }
}
