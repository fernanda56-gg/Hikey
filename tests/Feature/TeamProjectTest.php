<?php

use function Pest\Laravel\{actingAs, get, seed};

use App\Models\Company;
use App\Models\User;
use App\Models\Area;
use Database\Seeders\AreaSeeder;
use App\Models\Project;
use Spatie\Permission\Models\Role;

test('El usuario manager puede visualizar un proyecto en especifico', function () {
    /* se generan los roles */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* se crea usuario manager y empresa */
    /** @var \App\Models\User $manager */
    $manager = User::factory()->create();

    $company = Company::factory()->create([
        'owner_id' => $manager->id,
    ]);
    $manager->companies()->attach($company->id);

    /* se ejecuta el seed de areas */
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    /* se genera el proyecto */
    $project = Project::factory()->create([
        'by_user_id' => $manager->id,
        'area_id' => $area,
        'company_id' => $company->id,
    ]);

    actingAs($manager);
    get(route('projects.show', $project))
    ->assertOk()
    ->assertSee($project->name)
    ->assertSee($project->description);

    expect($manager->fresh()->hasRole('manager'))->toBeTrue();
});

test('El usuario admin puede visualizar un proyecto en especifico', function () {
    /* se generan los roles */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);
    Role::create(['name' => 'admin']);

    /* se crea usuario manager y empresa */
    /** @var \App\Models\User $manager */
    $manager = User::factory()->create();

    $company = Company::factory()->create([
        'owner_id' => $manager->id,
    ]);
    $manager->companies()->attach($company->id);

    /* se ejecuta el seed de areas */
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    /* se genera el proyecto */
    $project = Project::factory()->create([
        'by_user_id' => $manager->id,
        'area_id' => $area,
        'company_id' => $company->id,
    ]);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    actingAs($admin);
    get(route('projects.show', $project))
    ->assertOk()
    ->assertSee($project->name)
    ->assertSee($project->description);
});

test('El usuario manager puede ver la lista de miembros disponibles', function () {
    /* se generan los roles */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* se crea usuario manager y empresa */
    /** @var \App\Models\User $manager */
    $manager = User::factory()->create();

    $company = Company::factory()->create([
        'owner_id' => $manager->id,
    ]);
    $manager->companies()->attach($company->id);

    /* se ejecuta el seed de areas */
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    /* se genera el proyecto */
    $project = Project::factory()->create([
        'by_user_id' => $manager->id,
        'area_id' => $area,
        'company_id' => $company->id,
    ]);

    /* generar miembros de equipo */
    $members = User::factory()->count(3)->create();
    $company->member()->attach($members);

    $response = actingAs($manager)
    ->get(route('project-team.index', $project))
    ->assertOk();

    foreach($members as $user)
    {
        $response->assertSee($user->name);
    }
});

test('El usuario admin puede ver la lista de miembros disponibles', function () {
    /* se generan los roles */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);
    Role::create(['name' => 'admin']);

    /* se crea usuario manager y empresa */
    /** @var \App\Models\User $manager */
    $manager = User::factory()->create();

    $company = Company::factory()->create([
        'owner_id' => $manager->id,
    ]);
    $manager->companies()->attach($company->id);

    /* se ejecuta el seed de areas */
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    /* se genera el proyecto */
    $project = Project::factory()->create([
        'by_user_id' => $manager->id,
        'area_id' => $area,
        'company_id' => $company->id,
    ]);

    /* generar miembros de equipo */
    $members = User::factory()->count(3)->create();
    $company->member()->attach($members);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = actingAs($admin)
    ->get(route('project-team.index', $project))
    ->assertOk();

    foreach($members as $user)
    {
        $response->assertSee($user->name);
    }
});

// TODO: falta los test de agregar miembros de equipo, sacar miembro, asignar lider, quitar lider y talvez que se muestre la tabla de integrantes.
