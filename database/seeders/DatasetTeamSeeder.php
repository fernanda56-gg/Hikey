<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Team;
use App\Imports\ProjectTeamImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Seeder;

class DatasetTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // * se ejecuta el seeder
        Excel::import(new ProjectTeamImport, database_path('seeders/data/project_team.csv'));
    }
}
