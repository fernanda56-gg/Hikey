<?php

use function Pest\Laravel\{actingAs, get, seed};

use App\Models\Activity;
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

test('El usuario puede editar la información de actividades', function () {
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

    /* generar usuario líder de equipo */
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    $project->users()->attach($user, ['role' => 'Miembro']);
    app(ProjectTeamService::class)->changeRole($project, $user, 'Lider');

    expect($project->fresh()->leader->contains($user))->toBeTrue();
    expect($user->fresh()->hasRole('team-leader'))->toBeTrue();

    /* se crea la actividad */
    $activity = Activity::factory()->create([
        'project_id' => $project->id,
        'by_user_id' => $user->id
    ]);

    // * se realiza el test
    $response = actingAs($user)
    ->put(route('activities.update', $activity), [
        'name' => 'Actividad AB',
        'description' => $activity->description,
        'priority' => $activity->priority,
        'status' => $activity->status,
        'link' => $activity->link,
        'due_date' => now()->addDays(14)->toDateString(),
        'project_id' => $activity->project_id,
        'by_user_id' => $activity->by_user_id
    ]);

    $response->assertSessionHasNoErrors()
    ->assertRedirect();
});

test('El usuario no puede editar la información de actividad si no es líder', function () {
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

    /* se genera usuario normal */
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    $project->users()->attach($user, ['role' => 'Miembro']);

    /* se genera la actividad */
    $activity = Activity::factory()->create([
        'project_id' => $project->id,
        'by_user_id' => $manager->id
    ]);

    // * se realiza el test
    $response = actingAs($user)
    ->put(route('activities.update', $activity), [
        'name' => 'Actividad AB',
        'description' => $activity->description,
        'priority' => $activity->priority,
        'status' => $activity->status,
        'link' => $activity->link,
        'due_date' => now()->addDays(14)->toDateString(),
        'project_id' => $activity->project_id,
        'by_user_id' => $activity->by_user_id
    ]);

    $response->assertForbidden(); // ! se espera error 403
});

test('El usuario puede hacer soft delete y rescatar una actividad', function () {
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

    /* se genera la actividad y se elimina */
    $activity = Activity::factory()->create([
        'project_id' => $project->id,
        'by_user_id' => $manager->id
    ]);

    // * se realiza el test
    $activity->delete();
    expect($activity->fresh()->deleted_at)->not->toBeNull();

    $activity->restore();
    $activity->refresh();
    expect($activity->deleted_at)->toBeNull();
});

test('El usuario líder puede eliminar una actividad pero no rescatarla', function () {
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

    /* generar usuario líder de equipo */
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    $project->users()->attach($user, ['role' => 'Miembro']);
    app(ProjectTeamService::class)->changeRole($project, $user, 'Lider');

    expect($project->fresh()->leader->contains($user))->toBeTrue();
    expect($user->fresh()->hasRole('team-leader'))->toBeTrue();

    /* se crea la actividad */
    $activity = Activity::factory()->create([
        'project_id' => $project->id,
        'by_user_id' => $user->id
    ]);

    // * se realiza el test
    $activity->delete();
    expect($activity->fresh()->deleted_at)->not->toBeNull();

    $response = actingAs($user)
    ->put(route('activities.restore', $activity));

    $response->assertForbidden(); // ! error 403
});

test('El usuario puede eliminar definitivamente una actividad', function () {
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

    /* se genera la actividad y se elimina */
    $activity = Activity::factory()->create([
        'project_id' => $project->id,
        'by_user_id' => $manager->id
    ]);

    // * se realiza el test
    $activity->delete();
    expect($activity->fresh()->deleted_at)->not->toBeNull();

    $activity->forceDelete();
    $activity->refresh();

    expect(Activity::find($activity->id))->toBeNull();
    expect(Activity::withTrashed()->find($activity->id))->toBeNull();
});

test('El usuario líder puede eliminar pero no definitivamente una actividad', function () {
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

    /* generar usuario líder de equipo */
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    $project->users()->attach($user, ['role' => 'Miembro']);
    app(ProjectTeamService::class)->changeRole($project, $user, 'Lider');

    expect($project->fresh()->leader->contains($user))->toBeTrue();
    expect($user->fresh()->hasRole('team-leader'))->toBeTrue();

    /* se crea la actividad */
    $activity = Activity::factory()->create([
        'project_id' => $project->id,
        'by_user_id' => $user->id
    ]);

    // * se realiza el test
    $activity->delete();
    expect($activity->fresh()->deleted_at)->not->toBeNull();

    $response = actingAs($user)
    ->delete(route('activities.force-delete', $activity));

    $response->assertForbidden();
});

test('El usuario puede asignar a un responsable de actividad', function () {
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

    /* se generan a los miembros de equipo */
    $users = User::factory(3)->create();
    $company->member()->attach($users);
    $project->users()->attach($users, ['role' => 'Miembro']);
    $assign_member = $users->first();

    /* se crea la actividad */
    $activity = Activity::factory()->create([
        'project_id' => $project->id,
        'by_user_id' => $manager->id
    ]);

    // * se realiza el test
    $response = actingAs($manager)
    ->put(route('activities.assign', $activity), [
        'user_ids' => [$assign_member->id]
    ]);

    $response->assertSessionHasNoErrors()
    ->assertRedirect();
});

test('El usuario no puede asignar a un responsable si no es líder de equipo', function () {
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

    /* se generan a los miembros de equipo */
    $users = User::factory(3)->create();
    $company->member()->attach($users);
    $project->users()->attach($users, ['role' => 'Miembro']);
    $assign_member = $users->first();
    $other_user = $users->last();

    /* se crea la actividad */
    $activity = Activity::factory()->create([
        'project_id' => $project->id,
        'by_user_id' => $manager->id
    ]);

    // * se realiza el test
    // ? si redirige cuando hace la asignación pero no hace la asignación en la BD
    $response = actingAs($other_user)
    ->put(route('activities.assign', $activity), [
        'user_ids' => [$assign_member->id]
    ]);

    $response->assertRedirect();
    expect($activity->fresh()->users)->not->toContain($assign_member);
});

test('El usuario si puede actualizar el estatus del proyecto', function () {
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

    /* se crea la actividad */
    $activity = Activity::factory()->create([
        'project_id' => $project->id,
        'by_user_id' => $manager->id
    ]);

    $response = actingAs($manager)
    ->patch(route('activities.update-status', $activity), [
        'status' => 'En progreso'
    ]);

    $response->assertSessionHasNoErrors()
    ->assertRedirect();
});

test('El usuario no puede actualizar el estatus de actividad de otra empresa', function () {
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

    /* se crea la actividad */
    $activity = Activity::factory()->create([
        'project_id' => $project->id,
        'by_user_id' => $manager->id
    ]);

    /* se genera otro usuario */
    /** @var \App\Models\User $other_user */
    $other_user = User::factory()->create();

    // * se realiza el test
    $response = actingAs($other_user)
    ->patch(route('activities.update-status', $activity), [
        'status' => 'En progreso'
    ]);

    $response->assertRedirect();
    expect($activity->fresh()->status)->not->toBe('En progreso');
});

test('El usuario puede regresar una actividad', function () {
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

    /* generar usuario líder de equipo */
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    $project->users()->attach($user, ['role' => 'Miembro']);
    app(ProjectTeamService::class)->changeRole($project, $user, 'Lider');

    expect($project->fresh()->leader->contains($user))->toBeTrue();
    expect($user->fresh()->hasRole('team-leader'))->toBeTrue();

    /* se crea la actividad */
    $activity = Activity::factory()->create([
        'project_id' => $project->id,
        'by_user_id' => $user->id
    ]);

    // * se realiza el test
    $response = actingAs($user)
    ->patch(route('activities.return', $activity));

    $response->assertRedirect()
    ->assertSessionHasNoErrors();
});

test('El usuario no puede regresar una actividad', function () {
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

    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    $user->assignRole('manager');

    /* se crea la actividad */
    $activity = Activity::factory()->create([
        'project_id' => $project->id,
        'by_user_id' => $manager->id
    ]);

    // * se realiza el test
    $response = actingAs($user)
    ->patch(route('activities.return', $activity));

    $response->assertForbidden();
});

test('El usuario no puede marcar como completado una actividad', function () {
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

    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    $user->assignRole('manager');

    /* se crea la actividad */
    $activity = Activity::factory()->create([
        'project_id' => $project->id,
        'by_user_id' => $manager->id
    ]);

    // * se realiza el test
    $response = actingAs($user)
    ->patch(route('activities.mark-complete', $activity));

    $response->assertForbidden();
});


test('El usuario puede marcar como completado una actividad', function () {
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

    /* generar usuario líder de equipo */
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    $project->users()->attach($user, ['role' => 'Miembro']);
    app(ProjectTeamService::class)->changeRole($project, $user, 'Lider');

    expect($project->fresh()->leader->contains($user))->toBeTrue();
    expect($user->fresh()->hasRole('team-leader'))->toBeTrue();

    /* se crea la actividad */
    $activity = Activity::factory()->create([
        'project_id' => $project->id,
        'by_user_id' => $user->id
    ]);

    // * se realiza el test
    $response = actingAs($user)
    ->patch(route('activities.mark-complete', $activity));

    $response->assertRedirect()
    ->assertSessionHasNoErrors();
});
