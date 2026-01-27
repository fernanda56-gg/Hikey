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
        $leaderRole = Role::create(['name' => 'team-leader']);
        $userRole = Role::create(['name' => 'user']);

        //crear permisos

        //Cuentas de usuario
        $viewAccounts = Permission::create(['name' => 'view user accounts']);
        $createAccounts = Permission::create(['name' => 'create user accounts']);
        $editAccounts = Permission::create(['name' => 'edit user accounts']);
        $deleteAccounts = Permission::create(['name' => 'delete user accounts']);

        //Proyectos
        $viewProjects = Permission::create(['name' => 'view projects']);
        $createProjects = Permission::create(['name' => 'create projects']);
        $editProjects = Permission::create(['name' => 'edit projects']);
        $deleteProjects = Permission::create(['name' => 'delete projects']);

        //Empresas
        $viewCompanies = Permission::create(['name' => 'view companies']);
        $createCompanies = Permission::create(['name' => 'create companies']);
        $editCompanies = Permission::create(['name' => 'edit companies']);
        $deleteCompanies = Permission::create(['name' => 'delete companies']);
        $joinCompanies = Permission::create(['name' => 'join companies']);
        $redirectCompanies = Permission::create(['name' => 'redirect companies']);
        $listMembers = Permission::create(['name' => 'list company members']);
        $leaveCompany = Permission::create(['name' => 'leave company']);
        $checkCode = Permission::create(['name' => 'check code companies']);


        //Asignar permisos a roles
        //Admin
        $adminRole->givePermissionTo([
            //permisos para cuentas de usuario
            $viewAccounts,
            $createAccounts,
            $editAccounts,
            $deleteAccounts,
            //permisos para proyectos
            $viewProjects,
            $createProjects,
            $editProjects,
            $deleteProjects,
            //permisos para empresas
            $viewCompanies,
            $createCompanies,
            $editCompanies,
            $deleteCompanies,
            $joinCompanies,
            $redirectCompanies,
            $listMembers,
            $leaveCompany,
            $checkCode,
        ]);

        //Gerente de proyectos(manager)
        $managerRole->givePermissionTo([
            //permisos para proyectos
            $viewProjects,
            $createProjects,
            $editProjects,
            $deleteProjects,
            //permisos para empresas
            $viewCompanies,
            $editCompanies,
            $deleteCompanies,
            $redirectCompanies,
            $listMembers,
            $leaveCompany,
        ]);


        $userRole->givePermissionTo([
            //permisos para proyectos
            $viewProjects,
            //permisos para empresas
            $viewCompanies,
            $createCompanies,
            $joinCompanies,
            $checkCode,
            $redirectCompanies,
            $listMembers,
        ]);

    }
}

