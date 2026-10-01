<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Area;
use App\Imports\AreaImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Seeder;

class DatasetAreasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // * Se ejecuta el import
        Excel::import(new AreaImport, database_path('seeders/data/areas.csv'));
    }
}
