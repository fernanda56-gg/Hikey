<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Commands\AssignRole;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //crear roles
        $adminRole = Role::create(['name' => 'admin']);
        $managerRole = Role::create(['name' => 'manager']);
        $userRole = Role::create(['name' => 'user']);

        //crear permisos

        //Proyectos
        $viewProjects = Permission::create(['name' => 'view projects']);
        $createProjects = Permission::create(['name' => 'create projects']);
        $editProjects = Permission::create(['name' => 'edit projects']);
        $deleteProjects = Permission::create(['name' => 'delete projects']);

        //Asignar permisos a roles
        $adminRole->givePermissionTo(Permission::all());
        $userRole->givePermissionTo([
            $viewProjects,
            $createProjects,
            $editProjects,
            $deleteProjects,
        ]);

        //Asignar a usuario para el rol de admin
        $user = User::find(1);
        $user->assignRole('admin');

    }
}

