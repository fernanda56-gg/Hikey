<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class UsersImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?Model
    {
        // ? Modifica el formato de fecha de created_at del csv
        $created_at = !empty($row['created_at'])
        ? Carbon::parse($row['created_at'])->format('Y-m-d H:i:s')
        : now();

        // ! Se genera el registro de usuario
        $user = User::create([
            'name' => $row['name'],
            'last_name' => $row['last_name'],
            'email' => $row['email'],
            'email_verified_at' => now(),
            'password' => Hash::make($row['password']),
            'created_at' => $created_at,
            'updated_at' => $created_at,
        ]);

        // ! Se añade el rol al usuario
        if (!empty($row['spatie_rol'])) {
            $user->assignRole($row['spatie_rol']);
        }

        // ? Como ya se genero el registro se retorna null para que no haya duplicados
        return null;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ];
    }
}
