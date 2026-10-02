<?php

namespace App\Imports;

use App\Models\Client;
use App\Models\Company;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Override;

class ClientImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?Model
    {
        logger()->info('Fila recibida: ' . json_encode($row));

        // ? Modificación del formato de las fechas en el csv
        $created_at = !empty($row['created_at'])
        ? Carbon::parse($row['created_at'])->format('Y-m-d H:i:s')
        : now();

        // ? Se obtiene la empresa vinculada al cliente
        $company = Company::where('name', $row['company_name'])->first();

        // ! se genera el registro de cliente
        $client = Client::create([
            'name' => $row['name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'company_id' => $company->id,
            'created_at' => $created_at,
            'updated_at' => $created_at,
        ]);

        // ! se vincula el proyecto con el cliente
        if (!empty($row['proyectos_vinculados'])) {
            $projects_names = explode(',', $row['proyectos_vinculados']);

            foreach ($projects_names as $project_name) {
                $project_name = trim($project_name);

                $project = Project::where('name', $project_name)->first();
                $client->projects()->attach($project->id);
            }
        }

        return null;
    }

    #[Override]
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required',
            'phone' => 'required',
        ];
    }
}
