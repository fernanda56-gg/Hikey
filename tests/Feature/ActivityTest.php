<?php

use function Pest\Laravel\{actingAs, get, seed};

use App\Models\Area;
use App\Models\Company;
use App\Models\User;
use App\Models\Project;
use Database\Seeders\AreaSeeder;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\ProjectTeamService;

test('El usuario puede visualizar el tablón de actividades', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);
    Role::create(['name' => 'team-leader']);

    /* se genera el usuario manager y usuario normal */
    /** @var \App\Models\User $manager */
    $manager = User::factory()->create();

    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    /* se genera la empresa */
    $company = Company::factory()->create([
        'owner_id' => $manager->id,
    ]);
    $manager->companies()->attach($company->id); // vinculamos al usuario con la empresa
    $user->companies()->attach($company->id);

    /* se ejecuta el seed de areas */
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    /* se genera el proyecto */
    $project = Project::factory()->create([
        'by_user_id' => $user->id,
        'area_id' => $area,
        'company_id' => $company->id,
    ]);

    // * se realiza test
    $response = actingAs($manager);

    $response->get(route('activities-board.show', $project))
    ->assertOk()
    ->assertStatus(200);
});

test('El usuario de otra empresa no puede visualizar el tablón de otra empresa', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);
    Role::create(['name' => 'team-leader']);

    /* se generan los usuarios */
    /** @var \App\Models\User $manager_a */
    $manager_a = User::factory()->create();

    /** @var \App\Models\User $manager_b */
    $manager_b = User::factory()->create();

    /* se asigna roles */
    $manager_a->assignRole('manager');
    $manager_b->assignRole('manager');

    /* se genera la empresa */
    $company = Company::factory()->create([
        'owner_id' => $manager_a->id,
    ]);
    $manager_a->companies()->attach($company->id); // vinculamos al usuario con la empresa

    /* se ejecuta el seed de areas */
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    /* se genera el proyecto */
    $project = Project::factory()->create([
        'by_user_id' => $manager_a->id,
        'area_id' => $area,
        'company_id' => $company->id,
    ]);

    // * se realiza el test
    $response = actingAs($manager_b);

    $response->get(route('activities-board.show', $project))
    ->assertForbidden();
});

test('El usuario puede generar actividades', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);
    Role::create(['name' => 'team-leader']);

    /* se genera los usuarios */
    /** @var \App\Models\User $manager */
    $manager = User::factory()->create();

    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    /* se genera empresa */
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

    /* hacer líder de equipo a usuario */
    $project->users()->attach($user, ['role' => 'Miembro']);
    app(ProjectTeamService::class)->changeRole($project, $user, 'Lider');

    expect($project->fresh()->leader->contains($user))->toBeTrue();
    expect($user->fresh()->hasRole('team-leader'))->toBeTrue();

    // * realizar test
    $response = actingAs($user)
    ->post(route('activities.store', $project), [
        'name' => 'Actividad X',
        'description' => 'Descripción X',
        'priority' => 'Baja',
        'status' => 'Disponible',
        'link' => 'https://example.com/actividad-x',
        'due_date' => now()->addDays(14)->toDateString(),
        'project_id' => $project->id,
        'by_user_id' => $user->id,
    ]);

    $response->assertSessionHasNoErrors()
    ->assertRedirect();
});

test('El usuario de otra empresa no puede generar actividades', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);
    Role::create(['name' => 'team-leader']);

    /* se genera los usuarios */
    /** @var \App\Models\User $manager */
    $manager = User::factory()->create();

    /* se genera empresa */
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

    /* se genera el usuario de otra empresa */
    /** @var \App\Models\User $other_user */
    $other_user = User::factory()->create();

    $other_user->assignRole('manager');

    // * se realiza la prueba
    $response = actingAs($other_user)
    ->post(route('activities.store', $project), [
        'name' => 'Actividad X',
        'description' => 'Descripción X',
        'priority' => 'Baja',
        'status' => 'Disponible',
        'link' => 'https://example.com/actividad-x',
        'due_date' => now()->addDays(14)->toDateString(),
        'project_id' => $project->id,
        'by_user_id' => $other_user->id,
    ]);

    $response->assertForbidden();
});

