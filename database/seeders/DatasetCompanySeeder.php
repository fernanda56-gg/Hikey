<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Company;
use App\Imports\CompanyImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Seeder;

class DatasetCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // * Se ejecuta el import
        Excel::import(new CompanyImport, database_path('seeders/data/companies.csv'));
    }
}
