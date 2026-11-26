<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Activity;
use App\Models\ActivityComments;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Override;

class ActivityCommentImport implements ToModel, WithHeadingRow, WithValidation
{
    #[Override]
    public function model(array $row): ?Model
    {
        // ? Modificación de formato de fechas
        $created_at = !empty($row['created_at'])
        ? Carbon::parse($row['created_at'])->format('Y-m-d H:i:s')
        : now();

        // ? se obtiene al usuario y actividad
        $user = User::where('email', $row['user_email'])->first();
        $activity = Activity::where('name', $row['activity_name'])->first();

        // ! se genera el registro
        $comment = ActivityComments::create([
            'activity_id' => $activity->id,
            'user_id' => $user->id,
            'content' => $row['content'],
            'created_at' => $created_at,
            'updated_at' => null
        ]);

        return null;
    }

    #[Override]
    public function rules(): array
    {
        return [
            'content' => 'required|string'
        ];
    }
}
