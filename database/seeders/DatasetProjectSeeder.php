<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Project;
use App\Imports\ProjectImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Seeder;

class DatasetProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // * Se ejecuta el import
        Excel::import(new ProjectImport, database_path('seeders/data/projects.csv'));
    }
}
