<?php

namespace App\Imports;

use Override;
use App\Models\Activity;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ActivityImport implements ToModel, WithHeadingRow, WithValidation
{
    #[Override]
    public function model(array $row): ?Model
    {
        // ? Modificación de formato de fechas
        $created_at = !empty($row['created_at'])
        ? Carbon::parse($row['created_at'])->format('Y-m-d H:i:s')
        : now();

        $cleanDate = function($value) {
            if (empty($value) || strtolower(trim($value)) === 'null') {
                return null;
            }
            try {
                return Carbon::parse($value)->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return null;
            }
        };

        $due_date = $cleanDate($row['due_date'] ?? null);
        $start_date = $cleanDate($row['start_date'] ?? null);
        $completed_at = $cleanDate($row['completed_at'] ?? null);

        // ? se obtiene al proyecto y al usuario
        $project = Project::where('name', $row['project_name'])->first();
        $user = User::where('email', $row['user_email'])->first();

        // ! se genera el registro
        $activity = Activity::create([
            'name' => $row['name'],
            'description' => $row['description'],
            'priority' => $row['priority'],
            'status' => $row['status'],
            'link' => $row['link'],
            'due_date' => $due_date,
            'start_date' => $start_date,
            'completed_at' => $completed_at,
            'project_id' => $project->id,
            'by_user_id' => $user->id,
            'created_at' => $created_at,
            'updated_at' => null
        ]);

        // ! se agrega la información de los responsables de actividad
        $emails = collect(explode(',', (string) $row['responsable_email']))
            ->map(fn ($e) => trim($e))
            ->reject(fn ($e) => $e === '' || strtolower($e) === 'null')
            ->unique()
            ->values();

        // ? alertas de validación de responsables
        if ($emails->count() > 3) {
            throw new \RuntimeException("'{$row['name']}': más de 3 responsables.");
        }

        $ids = $emails->map(function ($email) use ($project, $row) {
            $user = User::where('email', $email)->first();

            if (! $user || ! $project->users()->where('users.id', $user->id)->exists()) {
                throw new \RuntimeException("'{$row['name']}': {$email} no está en el equipo de {$project->name}.");
            }

            return $user->id;
        });

        $activity->users()->sync($ids);

        return null;
    }

    #[Override]
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'description' => 'required|string',
            'priority' => 'required',
            'status' => 'required',
            'link' => 'required|url',
            'due_date' => 'nullable',
            'start_date' => 'nullable',
            'completed_at' => 'nullable'
        ];
    }
}
