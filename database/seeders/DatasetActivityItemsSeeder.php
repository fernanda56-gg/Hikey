<?php

namespace Database\Seeders;

use App\Imports\ActivityItemsImport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class DatasetActivityItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // * se ejecuta el excel
        Excel::import(new ActivityItemsImport, database_path('seeders/data/activity_items.csv'));
    }
}
