<?php

namespace App\Imports;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Override;

class ProjectTeamImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?Model
    {
        // ? modificación del formato fechas del csv
        $created_at = !empty($row['created_at'])
        ? Carbon::parse($row['created_at'])->format('Y-m-d H:i:s')
        : now();

        // ? se obtiene proyecto y usuario
        $project = Project::where('name', $row['project_name'])->first();
        $user = User::where('email', $row['user_email'])->first();

        // ! se genera el registro
        $team = Team::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'role' => $row['role'],
            'created_at' => $created_at,
            'updated_at' => $created_at,
        ]);

        return null;
    }

    #[Override]
    public function rules(): array
    {
        return [
            'role' => 'required'
        ];
    }
}
