<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\User;
use Spatie\Permission\Commands\AssignRole;

use function Symfony\Component\Clock\now;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userAdmin = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $userManager = User::factory()->create([
            'email' => 'manager@example.com'
        ]);

        $userTeamLeader = User::factory()->create([
            'email' => 'team-leader@example.com'
        ]);

        $user = User::factory()->create([
            'email' => 'user@example.com'
        ]);

        $userAdmin->assignRole('admin');
        $userManager->assignRole('manager');
        $userTeamLeader->assignRole('team-leader');
        $user->assignRole('user');

        Project::factory(10)->create([
            'by_user_id' => $userAdmin->id,
        ]);

        $company = Company::factory(1)->create([
            'owner_id' => $userManager->id,
        ]);

        $company = Company::first();
        $company->member()->attach($userManager->id, [
            'role' => 'propietario',
            'joined_at' => now(),
        ]);

    }
}
