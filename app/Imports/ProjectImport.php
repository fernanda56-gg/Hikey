<?php

namespace App\Imports;

use App\Models\Area;
use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Override;

class ProjectImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?Model
    {
        // ? Modificación del formato de las fechas en el csv
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
        //TODO: falta clientes y equipos

        $created_at = !empty($row['created_at'])
        ? Carbon::parse($row['created_at'])->format('Y-m-d H:i:s')
        : now();

        $start_date   = $cleanDate($row['start_date'] ?? null);
        $end_date     = $cleanDate($row['end_date'] ?? null);
        $completed_at = $cleanDate($row['completed_at'] ?? null);

        // ? Obtener el area, empresa y dueño del proyecto
        $owner = User::where('email', $row['user_email'])->first();
        $area = Area::where('name', $row['area_name'])->first();
        $company = Company::where('name', $row['company_name'])->first();

        $project = Project::create([
            'name' => $row['name'],
            'description' => $row['description'],
            'link' => $row['link'],
            'link_2' => $row['link_2'],
            'start_date' => $start_date,
            'end_date' => $end_date,
            'completed_at' => $completed_at,
            'by_user_id' => $owner->id,
            'area_id' => $area->id,
            'company_id' => $company->id,
            'created_at' => $created_at,
            'updated_at' => $created_at,
        ]);

        match ($row['status']) {
            'En progreso' => $project->markInProgress(),
            'Completado' => $project->markCompletedAt(),
            default => null,
        };

        return null;
    }

    #[Override]
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'description' => 'required|string',
            'link' => 'required|url',
            'link_2' => 'required|url',
            'start_date' => 'nullable',
            'end_date' => 'nullable',
            'completed_at' => 'nullable'
        ];
    }
}
