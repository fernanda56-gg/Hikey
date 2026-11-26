<?php

namespace Database\Seeders;

use App\Imports\ActivityImport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class DatasetActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // * se ejecuta el import
        Excel::import(new ActivityImport, database_path('seeders/data/activities.csv'));
    }
}
