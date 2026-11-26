<?php

namespace Database\Seeders;

use App\Imports\ActivityCommentImport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class DatasetActivityCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // * se ejecuta el seeder
        Excel::import(new ActivityCommentImport, database_path('seeders/data/activity_comments.csv'));
    }
}
