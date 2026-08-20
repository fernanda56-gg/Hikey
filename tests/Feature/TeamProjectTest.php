<?php

use function Pest\Laravel\{actingAs, get, seed};

use App\Models\Company;
use App\Models\User;
use App\Models\Area;
use Database\Seeders\AreaSeeder;
use App\Models\Project;
use App\Services\ProjectTeamService;
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

test('El usuario manager puede agregar miembros a un equipo', function () {
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
    ->post(route('project-team.store', $project), [
        'members_ids' => $members->pluck('id')->toArray()
    ]);
    $response->assertRedirect();

    foreach($members as $user){
        expect($project->fresh()->users->contains($user))->toBeTrue();
    }
});

test('El usuario admin puede agregar miembros a un equipo', function () {
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
    ->post(route('project-team.store', $project), [
        'members_ids' => $members->pluck('id')->toArray()
    ]);
    $response->assertRedirect();

    foreach($members as $user){
        expect($project->fresh()->users->contains($user))->toBeTrue();
    }
});

test('El usuario manager puede hacer líder a un integrante del equipo', function () {
    /* se generan los roles */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);
    Role::create(['name' => 'team-leader']);

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

    /* generar miembros de equipo y agregarlos a un proyecto */
    $members = User::factory()->count(3)->create();
    $company->member()->attach($members);
    $project->users()->attach($members, ['role' => 'Miembro']);

    $projectLeader = $members->first();


    $response = actingAs($manager)
    ->patch(route('project-team.update-role', [$project, $projectLeader]), [
        'role' => 'Lider'
    ])->assertRedirect();
    $response->assertSessionHasNoErrors();

    expect($project->fresh()->leader->contains($projectLeader))->toBeTrue();
    expect($company->fresh()->member->contains($projectLeader))->toBeTrue();


});

test('El usuario manager puede remover al lider de un equipo', function () {
    /* se generan los roles */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);
    Role::create(['name' => 'team-leader']);

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

    /* generar miembros de equipo y agregarlos a un proyecto */
    $members = User::factory()->count(3)->create();
    $company->member()->attach($members);
    $project->users()->attach($members, ['role' => 'Miembro']);

    /* se actualiza rol de lider */
    $projectLeader = $members->first();
    $project->users()->updateExistingPivot($projectLeader->id, ['role' => 'Lider']);

    $response = actingAs($manager)
    ->put(route('project-team.remove-leader', [$project, $projectLeader]), [
        'role' => 'Miembro'
    ])->assertRedirect();

    $response->assertSessionHasNoErrors();
    expect($project->fresh()->leader->contains($projectLeader))->toBeFalse();
    expect($company->fresh()->member->contains($projectLeader))->toBeTrue();
});

test('El usuario manager no puede sacar a un lider de equipo', function () {
    /* se generan los roles */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);
    Role::create(['name' => 'team-leader']);

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

    /* generar miembros de equipo y agregarlos a un proyecto */
    $members = User::factory()->count(3)->create();
    $company->member()->attach($members);
    $project->users()->attach($members, ['role' => 'Miembro']);

    /* se actualiza rol de lider */
    $projectLeader = $members->first();
    $project->users()->updateExistingPivot($projectLeader->id, ['role' => 'Lider']);

    actingAs($manager)
    ->delete(route('project-team.destroy', [$project, $projectLeader]), [
        'role' => 'Lider',
    ])->assertSessionHasErrors();

    expect($project->fresh()->leader->contains($projectLeader))->toBeTrue();
    expect($company->fresh()->member->contains($projectLeader))->toBeTrue();
});

test('El usuario manager puede sacar a un miembro del equipo', function () {
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

    /* generar miembros de equipo y agregarlos a un proyecto */
    $members = User::factory()->count(3)->create();
    $company->member()->attach($members);
    $project->users()->attach($members, ['role' => 'Miembro']);

    $removeMember = $members->first();

    $response = actingAs($manager)
    ->delete(route('project-team.destroy', [$project, $removeMember]), [
        'role' => 'Miembro'
    ]);

    $response->assertSessionHasNoErrors();
    expect($project->fresh()->users->contains($removeMember))->toBeFalse();
    expect($company->fresh()->member->contains($removeMember))->toBeTrue();
});



test('El usuario admin puede hacer lider a un integrante del equipo', function () {
    /* se generan los roles */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'team-leader']);

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

    /* generar miembros de equipo y agregarlos a un proyecto */
    $members = User::factory()->count(3)->create();
    $company->member()->attach($members);
    $project->users()->attach($members, ['role' => 'Miembro']);

    $projectLeader = $members->first();

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = actingAs($admin)
    ->patch(route('project-team.update-role', [$project, $projectLeader]), [
        'role' => 'Lider'
    ])->assertRedirect();
    $response->assertSessionHasNoErrors();

    expect($project->fresh()->leader->contains($projectLeader))->toBeTrue();
    expect($company->fresh()->member->contains($projectLeader))->toBeTrue();
});

test('El usuario admin puede remover al lider de un equipo', function () {
    /* se generan los roles */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);
    Role::create(['name' => 'team-leader']);
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

    /* generar miembros de equipo y agregarlos a un proyecto */
    $members = User::factory()->count(3)->create();
    $company->member()->attach($members);
    $project->users()->attach($members, ['role' => 'Miembro']);

    /* se actualiza rol de lider */
    $projectLeader = $members->first();
    app(ProjectTeamService::class)->changeRole($project, $projectLeader, 'Lider');

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = actingAs($admin)
    ->put(route('project-team.remove-leader', [$project, $projectLeader]), [
        'role' => 'Miembro'
    ])->assertRedirect();

    $response->assertSessionHasNoErrors();
    expect($project->fresh()->leader->contains($projectLeader))->toBeFalse();
    expect($company->fresh()->member->contains($projectLeader))->toBeTrue();
});

test('El usuario admin no puede sacar a un lider de equipo', function () {
    /* se generan los roles */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);
    Role::create(['name' => 'team-leader']);
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

    /* generar miembros de equipo y agregarlos a un proyecto */
    $members = User::factory()->count(3)->create();
    $company->member()->attach($members);
    $project->users()->attach($members, ['role' => 'Miembro']);

    /* se actualiza rol de lider */
    $projectLeader = $members->first();
    app(ProjectTeamService::class)->changeRole($project, $projectLeader, 'Lider');

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    actingAs($admin)
    ->delete(route('project-team.destroy', [$project, $projectLeader]), [
        'role' => 'Lider',
    ])->assertSessionHasErrors();

    expect($project->fresh()->leader->contains($projectLeader))->toBeTrue();
    expect($company->fresh()->member->contains($projectLeader))->toBeTrue();
});

test('El usuario admin puede sacar a un miembro del equipo', function () {
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

    /* generar miembros de equipo y agregarlos a un proyecto */
    $members = User::factory()->count(3)->create();
    $company->member()->attach($members);
    $project->users()->attach($members, ['role' => 'Miembro']);

    $removeMember = $members->first();

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = actingAs($admin)
    ->delete(route('project-team.destroy', [$project, $removeMember]), [
        'role' => 'Miembro'
    ]);

    $response->assertSessionHasNoErrors();
    expect($project->fresh()->users->contains($removeMember))->toBeFalse();
    expect($company->fresh()->member->contains($removeMember))->toBeTrue();
});


test('El lider de equipo puede agregar miembros al equipo', function () {
    /* se generan los roles */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);
    Role::create(['name' => 'team-leader']);

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

    /* generar a lider del equipo */
    $leader = User::factory()->create();
    $company->member()->attach($leader);
    $project->users()->attach($leader, ['role' => 'Miembro']);
    app(ProjectTeamService::class)->changeRole($project, $leader, 'Lider');

    expect($project->fresh()->leader->contains($leader))->toBeTrue();
    expect($leader->fresh()->hasRole('team-leader'))->toBeTrue();

    /* generar miembros de equipo */
    $members = User::factory()->count(3)->create();
    $company->member()->attach($members);

    $response = actingAs($leader)
    ->post(route('project-team.store', $project), [
        'members_ids' => $members->pluck('id')->toArray()
    ]);
    $response->assertRedirect()
    ->assertSessionHasNoErrors();

    foreach($members as $user){
        expect($project->fresh()->users->contains($user))->toBeTrue();
        expect($company->fresh()->member->contains($user))->toBeTrue();
    }
});

test('El lider de equipo puede sacar a un miembro del equipo', function () {
    /* se generan los roles */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);
    Role::create(['name' => 'team-leader']);

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

    /* generar a lider del equipo */
    $leader = User::factory()->create();
    $company->member()->attach($leader);
    $project->users()->attach($leader, ['role' => 'Miembro']);
    app(ProjectTeamService::class)->changeRole($project, $leader, 'Lider');

    expect($project->fresh()->leader->contains($leader))->toBeTrue();
    expect($leader->fresh()->hasRole('team-leader'))->toBeTrue();

    /* generar miembros del equipo */
    $members = User::factory(3)->create();
    $company->member()->attach($members);
    $project->users()->attach($members, ['role' => 'Miembro']);
    $removeMember = $members->first();

    $response = actingAs($leader)
    ->delete(route('project-team.destroy', [$project, $removeMember]), [
        'role' => 'Miembro'
    ]);

    $response->assertRedirect()
    ->assertSessionHasNoErrors();

    expect($project->fresh()->users->contains($removeMember))->toBeFalse();
    expect($company->fresh()->member->contains($removeMember))->toBeTrue();
});
