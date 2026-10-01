<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;
use Override;

class CompanyImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?Model
    {
        // ? Modifica el formato de fecha de created_at del csv
        $created_at = !empty($row['created_at'])
        ? Carbon::parse($row['created_at'])->format('Y-m-d H:i:s')
        : now();

        // ? Se obtiene al dueño de la empresa
        $owner = User::where('email', $row['owner_email'])->first();

        // ! Se genera el registro de empresa
        $company = Company::create([
            'name' => $row['name'],
            'email' => $row['email'],
            'address' => $row['address'],
            'city' => $row['city'],
            'country' => $row['country'],
            'phone' => $row['phone'],
            'web_address' => $row['web_address'],
            'tax_id' => $row['tax_id'],
            'company_code' => $row['company_code'],
            'owner_id' => $owner->id,
            'created_at' => $created_at,
            'updated_at' => $created_at,
        ]);

        // ! Se asigna el rol de Propietario a los dueños de empresa
        $company->member()->syncWithoutDetaching([
            $owner->id => ['role' => 'propietario']
        ]);

        // ! Se asigna el rol de Miembro a los usuarios de la empresa
        if (!empty($row['members_email'])) {
            $emails = explode(',', $row['members_email']);
            foreach ($emails as $email) {
                $email = trim($email);

                // ? Se valida que el campo email no este vació y que no este el email del dueño
                if (!empty($email) && $email !== $row['owner_email']) {
                    $member = User::where('email', $email)->first();
                    if ($member) {
                        $company->member()->syncWithoutDetaching([
                            $member->id => ['role' => 'miembro']
                        ]);
                    }
                }
            }
        }
        // ? Como ya se genero el registro se retorna null para que no haya duplicados
        return null;
    }

    #[Override]
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email',
            'address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
            'phone' => 'required|numeric',
            'web_address' => 'required|string',
            'tax_id' => 'required',
            'company_code' => 'required|string'
        ];
    }
}
