<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //USUARIOS
        $userAdmin = User::factory()->create([
            'email' => 'test@example.com',
        ])->assignRole('admin');

        $userManager = User::factory()->create([
            'email' => 'manager@example.com'
        ])->assignRole('manager');

        $userTeamLeader = User::factory()->create([
            'email' => 'team-leader@example.com'
        ])->assignRole('team-leader');

        $user = User::factory()->create([
            'email' => 'user@example.com'
        ])->assignRole('user');

        User::factory(20)->create()
            ->each(function ($user) {
                $user->assignRole('user');
            });

        //CREACIÓN DE COMPAÑÍA Y PROYECTOS
        $company = Company::factory(1)->create([
            'owner_id' => $userManager->id,
        ]);

        Project::factory(30)->create([
            'by_user_id' => $userManager->id,
            'company_id' => $company->first()->id,
        ]);

        $company = Company::first();
        $company->member()->attach($userManager->id, [
            'role' => 'propietario',
            'joined_at' => now(),
        ]);

    }
}
