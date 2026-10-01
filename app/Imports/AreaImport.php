<?php

namespace App\Imports;

use App\Models\Area;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Override;

class AreaImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?Model
    {
        // ? Modifica el formato de fecha de created_at del csv
        $created_at = !empty($row['created_at'])
        ? Carbon::parse($row['created_at'])->format('Y-m-d H:i:s')
        : now();

        // ! Se genera el registro de areas
        $area = Area::create([
            'name' => $row['name'],
            'created_at' => $created_at,
            'updated_at' => $created_at,
        ]);

        // ? Como ya se genero el registro se retorna null para que no haya duplicados
        return null;
    }

    #[Override]
    public function rules(): array
    {
        return [
            'name' => 'required|string',
        ];
    }
}
