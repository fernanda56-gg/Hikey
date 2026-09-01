<?php

use function Pest\Laravel\{actingAs, get, seed};

use App\Models\Client;
use App\Models\User;
use App\Models\Company;
use App\Models\Project;
use App\Models\Area;
use Spatie\Permission\Models\Role;
use Database\Seeders\AreaSeeder;

test('El usuario con rol MANAGER puede visualizar la lista de clientes de su empresa', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    $user->assignRole('manager');

    $company = Company::factory()->create([
        'owner_id' => $user->id,
    ]);
    $user->companies()->attach($company->id);

    $response = actingAs($user)
    ->get(route('clients.index'))
    ->assertStatus(200);
});

test('El usuario con rol ADMIN puede visualizar la lista de clientes', function () {
    /* se crea rol para usuario */
    Role::create(['name' => 'admin']);

    /* el usuario se genera */
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = actingAs($user)
    ->get(route('clients.index'))
    ->assertStatus(200);
});

test('Usuario con otro tipo de rol no puede visualizar la lista de clientes', function () {
    /* se crea rol para usuario */
    Role::create(['name' => 'user']);

    /* el usuario se genera */
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    $user->assignRole('user');

    $response = actingAs($user)
    ->get(route('clients.index'))
    ->assertForbidden(); // error 403
});

test('El usuario con rol MANAGER puede visualizar la lista de proyectos vinculados al cliente', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $manager */
    $manager = User::factory()->create();
    $manager->assignRole('manager');

    // se genera la empresa
    $company = Company::factory()->create([
        'owner_id' => $manager->id,
    ]);
    $manager->companies()->attach($company->id);

    //* creamos al cliente y lo vinculamos a la empresa
    $client = Client::factory()->create([
        'company_id' => $company->id,
    ]);

    // seed de las areas
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    //* se crea el proyecto y se vincula al proyecto con el cliente
    $project = Project::factory()->create([
        'company_id' => $company->id,
        'area_id' => $area->id,
        'by_user_id' => $manager->id,
    ]);
    $project->clients()->attach($client->id);

    // vamos a la ruta y esperamos que deje ver proyecto al que esta vinculado el cliente
    $response = actingAs($manager)
    ->get(route('clients.projects', $client))
    ->assertStatus(200)
    ->assertSessionHasNoErrors();
});

test('El usuario con rol ADMIN puede visualizar la lista de proyectos vinculados al cliente', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);
    Role::create(['name' => 'admin']);

    /* el usuario se genera */
    /** @var \App\Models\User $manager */
    $manager = User::factory()->create();
    $manager->assignRole('manager');

    // se genera la empresa
    $company = Company::factory()->create([
        'owner_id' => $manager->id,
    ]);
    $manager->companies()->attach($company->id);

    //* creamos al cliente y lo vinculamos a la empresa
    $client = Client::factory()->create([
        'company_id' => $company->id,
    ]);

    // seed de las areas
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    //* se crea el proyecto y se vincula al proyecto con el cliente
    $project = Project::factory()->create([
        'company_id' => $company->id,
        'area_id' => $area->id,
        'by_user_id' => $manager->id,
    ]);
    $project->clients()->attach($client->id);

    //*generamos el admin
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = actingAs($admin)
    ->get(route('clients.projects', $client))
    ->assertStatus(200)
    ->assertSessionHasNoErrors();
});

test('El usuario con rol MANAGER puede editar la info del cliente', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $manager */
    $manager = User::factory()->create();
    $manager->assignRole('manager');

    // se genera la empresa
    $company = Company::factory()->create([
        'owner_id' => $manager->id,
    ]);
    $manager->companies()->attach($company->id);

    //* creamos al cliente y lo vinculamos a la empresa
    $client = Client::factory()->create([
        'company_id' => $company->id,
    ]);

    // seed de las areas
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    //* se crea el proyecto y se vincula al proyecto con el cliente
    $project = Project::factory()->create([
        'company_id' => $company->id,
        'area_id' => $area->id,
        'by_user_id' => $manager->id,
    ]);
    $project->clients()->attach($client->id);

    //* se actualiza la info del cliente
    $response = actingAs($manager)
    ->put(route('clients.update', $client), [
        'name' => $client->name,
        'email' => 'radical@test.com',
        'phone' => '523316924566',
        'company_id' => $company->id,
    ]);

    $response->assertSessionHasNoErrors()
    ->assertRedirect(route('projects.show', $project));
    $client->refresh();

    expect(Client::where('email', 'radical@test.com', 'phone', '523316924566'));
});

test('El usuario con rol ADMIN puede editar la info del cliente', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);
    Role::create(['name' => 'admin']);

    /* el usuario se genera */
    /** @var \App\Models\User $manager */
    $manager = User::factory()->create();
    $manager->assignRole('manager');

    // se genera la empresa
    $company = Company::factory()->create([
        'owner_id' => $manager->id,
    ]);
    $manager->companies()->attach($company->id);

    //* creamos al cliente y lo vinculamos a la empresa
    $client = Client::factory()->create([
        'company_id' => $company->id,
    ]);

    // seed de las areas
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    //* se crea el proyecto y se vincula al proyecto con el cliente
    $project = Project::factory()->create([
        'company_id' => $company->id,
        'area_id' => $area->id,
        'by_user_id' => $manager->id,
    ]);
    $project->clients()->attach($client->id);

    //*generamos el admin
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    //* se actualiza la info del cliente
    $response = actingAs($admin)
    ->put(route('clients.update', $client), [
        'name' => $client->name,
        'email' => 'radical@test.com',
        'phone' => '523316924566',
        'company_id' => $company->id,
    ]);

    $response->assertSessionHasNoErrors()
    ->assertRedirect(route('projects.show', $project));
    $client->refresh();

    expect(Client::where('email', 'radical@test.com', 'phone', '523316924566'));
});

test('El usuario con rol MANAGER puede hacer soft delete y restaurar clientes', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $manager */
    $manager = User::factory()->create();
    $manager->assignRole('manager');

    // se genera la empresa
    $company = Company::factory()->create([
        'owner_id' => $manager->id,
    ]);
    $manager->companies()->attach($company->id);

    //* creamos al cliente y lo vinculamos a la empresa
    $client = Client::factory()->create([
        'company_id' => $company->id,
    ]);

    // seed de las areas
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    //* se crea el proyecto y se vincula al proyecto con el cliente
    $project = Project::factory()->create([
        'company_id' => $company->id,
        'area_id' => $area->id,
        'by_user_id' => $manager->id,
    ]);
    $project->clients()->attach($client->id);

    $response = actingAs($manager)
    ->delete(route('clients.destroy', $client));

    expect($client->fresh()->deleted_at)->not->toBeNull();
    $client->restore();
    $client->refresh();

    expect($client->deleted_at)->toBeNull();
});

test('El usuario con rol MANAGER puede hacer soft delete y eliminar definitivamente clientes', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $manager */
    $manager = User::factory()->create();
    $manager->assignRole('manager');

    // se genera la empresa
    $company = Company::factory()->create([
        'owner_id' => $manager->id,
    ]);
    $manager->companies()->attach($company->id);

    //* creamos al cliente y lo vinculamos a la empresa
    $client = Client::factory()->create([
        'company_id' => $company->id,
    ]);

    // seed de las areas
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    //* se crea el proyecto y se vincula al proyecto con el cliente
    $project = Project::factory()->create([
        'company_id' => $company->id,
        'area_id' => $area->id,
        'by_user_id' => $manager->id,
    ]);
    $project->clients()->attach($client->id);

    $response = actingAs($manager)
    ->delete(route('clients.destroy', $client));

    expect($client->fresh()->deleted_at)->not->toBeNull();
    $client->forceDelete();
    $client->refresh();

    expect(Client::find($client->id))->toBeNull();
});

test('El usuario con rol ADMIN puede hacer soft delete y restaurar clientes', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);
    Role::create(['name' => 'admin']);

    /* el usuario se genera */
    /** @var \App\Models\User $manager */
    $manager = User::factory()->create();
    $manager->assignRole('manager');

    // se genera la empresa
    $company = Company::factory()->create([
        'owner_id' => $manager->id,
    ]);
    $manager->companies()->attach($company->id);

    //* creamos al cliente y lo vinculamos a la empresa
    $client = Client::factory()->create([
        'company_id' => $company->id,
    ]);

    // seed de las areas
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    //* se crea el proyecto y se vincula al proyecto con el cliente
    $project = Project::factory()->create([
        'company_id' => $company->id,
        'area_id' => $area->id,
        'by_user_id' => $manager->id,
    ]);
    $project->clients()->attach($client->id);

    //*generamos el admin
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = actingAs($admin)
    ->delete(route('clients.destroy', $client));

    expect($client->fresh()->deleted_at)->not->toBeNull();
    $client->restore();
    $client->refresh();

    expect($client->deleted_at)->toBeNull();
});

test('El usuario con rol ADMIN puede hacer soft delete y eliminar definitivamente clientes', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);
    Role::create(['name' => 'admin']);

    /* el usuario se genera */
    /** @var \App\Models\User $manager */
    $manager = User::factory()->create();
    $manager->assignRole('manager');

    // se genera la empresa
    $company = Company::factory()->create([
        'owner_id' => $manager->id,
    ]);
    $manager->companies()->attach($company->id);

    //* creamos al cliente y lo vinculamos a la empresa
    $client = Client::factory()->create([
        'company_id' => $company->id,
    ]);

    // seed de las areas
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    //* se crea el proyecto y se vincula al proyecto con el cliente
    $project = Project::factory()->create([
        'company_id' => $company->id,
        'area_id' => $area->id,
        'by_user_id' => $manager->id,
    ]);
    $project->clients()->attach($client->id);

    //*generamos el admin
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = actingAs($admin)
    ->delete(route('clients.destroy', $client));

    expect($client->fresh()->deleted_at)->not->toBeNull();
    $client->forceDelete();
    $client->refresh();

    expect(Client::find($client->id))->toBeNull();
});

test('El usuario con rol MANAGER puede crear clientes', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $manager */
    $manager = User::factory()->create();
    $manager->assignRole('manager');

    // se genera la empresa
    $company = Company::factory()->create([
        'owner_id' => $manager->id,
    ]);
    $manager->companies()->attach($company->id);

    // seed de las areas
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    // se crea el proyecto
    $project = Project::factory()->create([
        'company_id' => $company->id,
        'area_id'    => $area->id,
        'by_user_id' => $manager->id,
    ]);

    $response = actingAs($manager)
        ->post(route('clients.store', $project), [
            'name'       => 'Jane Doe',
            'email'      => 'doe31@example.com',
            'phone'      => '523314789522',
            'project_id' => $project->id, // se vincula al cliente con el proyecto
        ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('projects.show', $project->id));

    // verificamos que el cliente exista
    expect(Client::where('email', 'doe31@example.com', 'project_id', $project->id));

    // se recupera al cliente
    $client = Client::where('email', 'doe31@example.com')
        ->where('company_id', $company->id)
        ->first();

    //* se comprueba que exista el registro en la tabla pivote
    $this->assertDatabaseHas('client_project', [
        'client_id'  => $client->id,
        'project_id' => $project->id,
    ]);
});

test('El usuario con rol ADMIN puede crear clientes', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);
    Role::create(['name' => 'admin']);

    /* el usuario se genera */
    /** @var \App\Models\User $manager */
    $manager = User::factory()->create();
    $manager->assignRole('manager');

    // se genera la empresa
    $company = Company::factory()->create([
        'owner_id' => $manager->id,
    ]);
    $manager->companies()->attach($company->id);

    // seed de las areas
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    // se crea el proyecto
    $project = Project::factory()->create([
        'company_id' => $company->id,
        'area_id'    => $area->id,
        'by_user_id' => $manager->id,
    ]);

    //* generamos el admin
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = actingAs($admin)
        ->post(route('clients.store', $project), [
            'name'       => 'Jane Doe',
            'email'      => 'doe31@example.com',
            'phone'      => '523314789522',
            'project_id' => $project->id, // se vincula al cliente con el proyecto
        ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('projects.show', $project->id));

    // verificamos que el cliente exista
    expect(Client::where('email', 'doe31@example.com', 'project_id', $project->id));

    // se recupera al cliente
    $client = Client::where('email', 'doe31@example.com')
        ->where('company_id', $company->id)
        ->first();

    //* se comprueba que exista el registro en la tabla pivote
    $this->assertDatabaseHas('client_project', [
        'client_id'  => $client->id,
        'project_id' => $project->id,
    ]);
});

test('El usuario con rol MANAGER puede vincular a un cliente con un proyecto', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $manager */
    $manager = User::factory()->create();
    $manager->assignRole('manager');

    // se genera la empresa
    $company = Company::factory()->create([
        'owner_id' => $manager->id,
    ]);
    $manager->companies()->attach($company->id);

    //* creamos al cliente y lo vinculamos a la empresa
    $client = Client::factory()->create([
        'company_id' => $company->id,
    ]);

    // seed de las areas
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    // se crea e proyecto
    $project = Project::factory()->create([
        'company_id' => $company->id,
        'area_id' => $area->id,
        'by_user_id' => $manager->id,
    ]);

    $response = actingAs($manager)
    ->post(route('clients.projects.attach', $project), [
        'client_id' => $client->id,
    ]);

    $response->assertSessionHasNoErrors()
    ->assertRedirect();

    //* se comprueba que exista el registro en la tabla pivote
    $this->assertDatabaseHas('client_project', [
        'client_id'  => $client->id,
        'project_id' => $project->id,
    ]);
});

test('El usuario con rol ADMIN puede vincular a un cliente con un proyecto', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);
    Role::create(['name' => 'admin']);

    /* el usuario se genera */
    /** @var \App\Models\User $manager */
    $manager = User::factory()->create();
    $manager->assignRole('manager');


    // se genera la empresa
    $company = Company::factory()->create([
        'owner_id' => $manager->id,
    ]);
    $manager->companies()->attach($company->id);

    //* creamos al cliente y lo vinculamos a la empresa
    $client = Client::factory()->create([
        'company_id' => $company->id,
    ]);

    // seed de las areas
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    // se crea e proyecto
    $project = Project::factory()->create([
        'company_id' => $company->id,
        'area_id' => $area->id,
        'by_user_id' => $manager->id,
    ]);

    //* generamos el admin
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = actingAs($admin)
    ->post(route('clients.projects.attach', $project), [
        'client_id' => $client->id,
    ]);

    $response->assertSessionHasNoErrors()
    ->assertRedirect();

    //* se comprueba que exista el registro en la tabla pivote
    $this->assertDatabaseHas('client_project', [
        'client_id'  => $client->id,
        'project_id' => $project->id,
    ]);
});

test('El usuario con rol MANAGER puede desvincular a un cliente de un proyecto', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $manager */
    $manager = User::factory()->create();
    $manager->assignRole('manager');


    // se genera la empresa
    $company = Company::factory()->create([
        'owner_id' => $manager->id,
    ]);
    $manager->companies()->attach($company->id);

    //* creamos al cliente y lo vinculamos a la empresa
    $client = Client::factory()->create([
        'company_id' => $company->id,
    ]);

    // seed de las areas
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    // se crea e proyecto
    $project = Project::factory()->create([
        'company_id' => $company->id,
        'area_id' => $area->id,
        'by_user_id' => $manager->id,
    ]);
    $project->clients()->attach($client->id);

    //* se comprueba que exista el registro en la tabla pivote
    $this->assertDatabaseHas('client_project', [
        'client_id'  => $client->id,
        'project_id' => $project->id,
    ]);

    $response = actingAs($manager)
    ->delete(route('clients.projects.detach', [$client, $project]));

    $response->assertSessionHasNoErrors()
    ->assertRedirect();

    //* se comprueba que no exista el registro en la tabla pivote
    $this->assertDatabaseMissing('client_project', [
        'client_id'  => $client->id,
        'project_id' => $project->id,
    ]);
});

test('El usuario con rol ADMIN puede desvincular a un cliente de un proyecto', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);
    Role::create(['name' => 'admin']);

    /* el usuario se genera */
    /** @var \App\Models\User $manager */
    $manager = User::factory()->create();
    $manager->assignRole('manager');


    // se genera la empresa
    $company = Company::factory()->create([
        'owner_id' => $manager->id,
    ]);
    $manager->companies()->attach($company->id);

    //* creamos al cliente y lo vinculamos a la empresa
    $client = Client::factory()->create([
        'company_id' => $company->id,
    ]);

    // seed de las areas
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    // se crea e proyecto
    $project = Project::factory()->create([
        'company_id' => $company->id,
        'area_id' => $area->id,
        'by_user_id' => $manager->id,
    ]);
    $project->clients()->attach($client->id);

    //* se comprueba que exista el registro en la tabla pivote
    $this->assertDatabaseHas('client_project', [
        'client_id'  => $client->id,
        'project_id' => $project->id,
    ]);

    //* generamos el admin
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = actingAs($admin)
    ->delete(route('clients.projects.detach', [$client, $project]));

    $response->assertSessionHasNoErrors()
    ->assertRedirect();

    //* se comprueba que no exista el registro en la tabla pivote
    $this->assertDatabaseMissing('client_project', [
        'client_id'  => $client->id,
        'project_id' => $project->id,
    ]);
});

test('El usuario puede filtrar por nombre del cliente', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();
    $admin->assignRole('admin');


    //* generamos los otros 2 usuarios
    $user_a = User::factory()->create();
    $user_b = User::factory()->create();
    $user_c = User::factory()->create();

    // se genera las empresas
    $company_a = Company::factory()->create([
        'name' => 'Empresa A',
        'owner_id' => $user_a->id,
    ]);
    $user_a->companies()->attach($company_a->id);

    $company_b = Company::factory()->create([
        'name' => 'Empresa B',
        'owner_id' => $user_b->id,
    ]);
    $user_b->companies()->attach($company_b->id);

    $company_c = Company::factory()->create([
        'name' => 'Empresa C',
        'owner_id' => $user_c->id,
    ]);
    $user_c->companies()->attach($company_c->id);

    //* creamos al cliente y lo vinculamos a la empresa
    $client_a = Client::factory()->create([
        'name' => 'Juan Hernandez',
        'company_id' => $company_a->id,
    ]);

    $client_b = Client::factory()->create([
        'name' => 'Esteban Sedano',
        'company_id' => $company_b->id,
    ]);

    $client_c = Client::factory()->create([
        'name' => 'Bruno Ruiz',
        'company_id' => $company_c->id,
    ]);

    // seed de las areas
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    //* se crea los proyectos y se vincula al proyecto con el cliente
    $project_a = Project::factory()->create([
        'name' => 'Proyecto MX',
        'company_id' => $company_a->id,
        'area_id' => $area->id,
        'by_user_id' => $user_a->id,
    ]);
    $project_a->clients()->attach($client_a->id);

    $project_b = Project::factory()->create([
        'name' => 'Proyecto USA',
        'company_id' => $company_b->id,
        'area_id' => $area->id,
        'by_user_id' => $user_b->id,
    ]);
    $project_b->clients()->attach($client_b->id);

    $project_c = Project::factory()->create([
        'name' => 'Proyecto CAN',
        'company_id' => $company_c->id,
        'area_id' => $area->id,
        'by_user_id' => $user_c->id,
    ]);
    $project_c->clients()->attach($client_c->id);

    actingAs($admin);

    //* se filtra la información
    $filter = Client::filter([
        'name' => 'Esteban Sedano',
    ])->get();

    expect($filter)->toHaveCount(1)
    ->and($filter->first()->id)->toBe($client_b->id);
});

test('El usuario puede filtrar por nombre del proyecto', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    //* generamos los otros 2 usuarios
    $user_a = User::factory()->create();
    $user_b = User::factory()->create();
    $user_c = User::factory()->create();

    // se genera las empresas
    $company_a = Company::factory()->create([
        'name' => 'Empresa A',
        'owner_id' => $user_a->id,
    ]);
    $user_a->companies()->attach($company_a->id);

    $company_b = Company::factory()->create([
        'name' => 'Empresa B',
        'owner_id' => $user_b->id,
    ]);
    $user_b->companies()->attach($company_b->id);

    $company_c = Company::factory()->create([
        'name' => 'Empresa C',
        'owner_id' => $user_c->id,
    ]);
    $user_c->companies()->attach($company_c->id);

    //* creamos al cliente y lo vinculamos a la empresa
    $client_a = Client::factory()->create([
        'name' => 'Juan Hernandez',
        'company_id' => $company_a->id,
    ]);

    $client_b = Client::factory()->create([
        'name' => 'Esteban Sedano',
        'company_id' => $company_b->id,
    ]);

    $client_c = Client::factory()->create([
        'name' => 'Bruno Ruiz',
        'company_id' => $company_c->id,
    ]);

    // seed de las areas
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    //* se crea los proyectos y se vincula al proyecto con el cliente
    $project_a = Project::factory()->create([
        'name' => 'Proyecto MX',
        'company_id' => $company_a->id,
        'area_id' => $area->id,
        'by_user_id' => $user_a->id,
    ]);
    $project_a->clients()->attach($client_a->id);

    $project_b = Project::factory()->create([
        'name' => 'Proyecto USA',
        'company_id' => $company_b->id,
        'area_id' => $area->id,
        'by_user_id' => $user_b->id,
    ]);
    $project_b->clients()->attach($client_b->id);

    $project_c = Project::factory()->create([
        'name' => 'Proyecto CAN',
        'company_id' => $company_c->id,
        'area_id' => $area->id,
        'by_user_id' => $user_c->id,
    ]);
    $project_c->clients()->attach($client_c->id);

    actingAs($admin);

    //* se filtra la información
    $filter = Client::filter([
        'projectName' => 'Proyecto MX',
    ])->get();

    expect($filter)->toHaveCount(1)
    ->and($filter->first()->id)->toBe($client_a->id);
});

test('El usuario puede filtrar por nombre de la empresa', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    //* generamos los otros 2 usuarios
    $user_a = User::factory()->create();
    $user_b = User::factory()->create();
    $user_c = User::factory()->create();

    // se genera las empresas
    $company_a = Company::factory()->create([
        'name' => 'Empresa A',
        'owner_id' => $user_a->id,
    ]);
    $user_a->companies()->attach($company_a->id);

    $company_b = Company::factory()->create([
        'name' => 'Empresa B',
        'owner_id' => $user_b->id,
    ]);
    $user_b->companies()->attach($company_b->id);

    $company_c = Company::factory()->create([
        'name' => 'Empresa C',
        'owner_id' => $user_c->id,
    ]);
    $user_c->companies()->attach($company_c->id);

    //* creamos al cliente y lo vinculamos a la empresa
    $client_a = Client::factory()->create([
        'name' => 'Juan Hernandez',
        'company_id' => $company_a->id,
    ]);

    $client_b = Client::factory()->create([
        'name' => 'Esteban Sedano',
        'company_id' => $company_b->id,
    ]);

    $client_c = Client::factory()->create([
        'name' => 'Bruno Ruiz',
        'company_id' => $company_c->id,
    ]);

    // seed de las areas
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    //* se crea los proyectos y se vincula al proyecto con el cliente
    $project_a = Project::factory()->create([
        'name' => 'Proyecto MX',
        'company_id' => $company_a->id,
        'area_id' => $area->id,
        'by_user_id' => $user_a->id,
    ]);
    $project_a->clients()->attach($client_a->id);

    $project_b = Project::factory()->create([
        'name' => 'Proyecto USA',
        'company_id' => $company_b->id,
        'area_id' => $area->id,
        'by_user_id' => $user_b->id,
    ]);
    $project_b->clients()->attach($client_b->id);

    $project_c = Project::factory()->create([
        'name' => 'Proyecto CAN',
        'company_id' => $company_c->id,
        'area_id' => $area->id,
        'by_user_id' => $user_c->id,
    ]);
    $project_c->clients()->attach($client_c->id);

    actingAs($admin);

    //* se filtra la información
    $filter = Client::filter([
        'companyName' => 'Empresa A',
    ])->get();

    expect($filter)->toHaveCount(1)
    ->and($filter->first()->id)->toBe($client_a->id);
});

test('El usuario no introduce nada en los filtros y retorna todos los registros', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    //* generamos los otros 2 usuarios
    $user_a = User::factory()->create();
    $user_b = User::factory()->create();
    $user_c = User::factory()->create();

    // se genera las empresas
    $company_a = Company::factory()->create([
        'name' => 'Empresa A',
        'owner_id' => $user_a->id,
    ]);
    $user_a->companies()->attach($company_a->id);

    $company_b = Company::factory()->create([
        'name' => 'Empresa B',
        'owner_id' => $user_b->id,
    ]);
    $user_b->companies()->attach($company_b->id);

    $company_c = Company::factory()->create([
        'name' => 'Empresa C',
        'owner_id' => $user_c->id,
    ]);
    $user_c->companies()->attach($company_c->id);

    //* creamos al cliente y lo vinculamos a la empresa
    $client_a = Client::factory()->create([
        'name' => 'Juan Hernandez',
        'company_id' => $company_a->id,
    ]);

    $client_b = Client::factory()->create([
        'name' => 'Esteban Sedano',
        'company_id' => $company_b->id,
    ]);

    $client_c = Client::factory()->create([
        'name' => 'Bruno Ruiz',
        'company_id' => $company_c->id,
    ]);

    // seed de las areas
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    //* se crea los proyectos y se vincula al proyecto con el cliente
    $project_a = Project::factory()->create([
        'name' => 'Proyecto MX',
        'company_id' => $company_a->id,
        'area_id' => $area->id,
        'by_user_id' => $user_a->id,
    ]);
    $project_a->clients()->attach($client_a->id);

    $project_b = Project::factory()->create([
        'name' => 'Proyecto USA',
        'company_id' => $company_b->id,
        'area_id' => $area->id,
        'by_user_id' => $user_b->id,
    ]);
    $project_b->clients()->attach($client_b->id);

    $project_c = Project::factory()->create([
        'name' => 'Proyecto CAN',
        'company_id' => $company_c->id,
        'area_id' => $area->id,
        'by_user_id' => $user_c->id,
    ]);
    $project_c->clients()->attach($client_c->id);

    actingAs($admin);

    //* se filtra la información
    $filter = Client::filter([
        'name' => '',
        'projectName' => '',
        'companyName' => '',
    ])->get();

    expect($filter)->toHaveCount(3);
});

test('El usuario utiliza todos los filtros', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    //* generamos los otros 2 usuarios
    $user_a = User::factory()->create();
    $user_b = User::factory()->create();
    $user_c = User::factory()->create();

    // se genera las empresas
    $company_a = Company::factory()->create([
        'name' => 'Empresa A',
        'owner_id' => $user_a->id,
    ]);
    $user_a->companies()->attach($company_a->id);

    $company_b = Company::factory()->create([
        'name' => 'Empresa B',
        'owner_id' => $user_b->id,
    ]);
    $user_b->companies()->attach($company_b->id);

    $company_c = Company::factory()->create([
        'name' => 'Empresa C',
        'owner_id' => $user_c->id,
    ]);
    $user_c->companies()->attach($company_c->id);

    //* creamos al cliente y lo vinculamos a la empresa
    $client_a = Client::factory()->create([
        'name' => 'Juan Hernandez',
        'company_id' => $company_a->id,
    ]);

    $client_b = Client::factory()->create([
        'name' => 'Esteban Sedano',
        'company_id' => $company_b->id,
    ]);

    $client_c = Client::factory()->create([
        'name' => 'Bruno Ruiz',
        'company_id' => $company_c->id,
    ]);

    // seed de las areas
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    //* se crea los proyectos y se vincula al proyecto con el cliente
    $project_a = Project::factory()->create([
        'name' => 'Proyecto MX',
        'company_id' => $company_a->id,
        'area_id' => $area->id,
        'by_user_id' => $user_a->id,
    ]);
    $project_a->clients()->attach($client_a->id);

    $project_b = Project::factory()->create([
        'name' => 'Proyecto USA',
        'company_id' => $company_b->id,
        'area_id' => $area->id,
        'by_user_id' => $user_b->id,
    ]);
    $project_b->clients()->attach($client_b->id);

    $project_c = Project::factory()->create([
        'name' => 'Proyecto CAN',
        'company_id' => $company_c->id,
        'area_id' => $area->id,
        'by_user_id' => $user_c->id,
    ]);
    $project_c->clients()->attach($client_c->id);

    actingAs($admin);

    //* se filtra la información
    $filter = Client::filter([
        'name' => 'Esteban Sedano',
        'projectName' => 'Proyecto USA',
        'companyName' => 'Empresa B',
    ])->get();

    expect($filter)->toHaveCount(1)
    ->and($filter->first()->id)->toBe($client_b->id);
});

test('El filtro no retorna nada si no hay coincidencias', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    //* generamos los otros 2 usuarios
    $user_a = User::factory()->create();
    $user_b = User::factory()->create();
    $user_c = User::factory()->create();

    // se genera las empresas
    $company_a = Company::factory()->create([
        'name' => 'Empresa A',
        'owner_id' => $user_a->id,
    ]);
    $user_a->companies()->attach($company_a->id);

    $company_b = Company::factory()->create([
        'name' => 'Empresa B',
        'owner_id' => $user_b->id,
    ]);
    $user_b->companies()->attach($company_b->id);

    $company_c = Company::factory()->create([
        'name' => 'Empresa C',
        'owner_id' => $user_c->id,
    ]);
    $user_c->companies()->attach($company_c->id);

    //* creamos al cliente y lo vinculamos a la empresa
    $client_a = Client::factory()->create([
        'name' => 'Juan Hernandez',
        'company_id' => $company_a->id,
    ]);

    $client_b = Client::factory()->create([
        'name' => 'Esteban Sedano',
        'company_id' => $company_b->id,
    ]);

    $client_c = Client::factory()->create([
        'name' => 'Bruno Ruiz',
        'company_id' => $company_c->id,
    ]);

    // seed de las areas
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    //* se crea los proyectos y se vincula al proyecto con el cliente
    $project_a = Project::factory()->create([
        'name' => 'Proyecto MX',
        'company_id' => $company_a->id,
        'area_id' => $area->id,
        'by_user_id' => $user_a->id,
    ]);
    $project_a->clients()->attach($client_a->id);

    $project_b = Project::factory()->create([
        'name' => 'Proyecto USA',
        'company_id' => $company_b->id,
        'area_id' => $area->id,
        'by_user_id' => $user_b->id,
    ]);
    $project_b->clients()->attach($client_b->id);

    $project_c = Project::factory()->create([
        'name' => 'Proyecto CAN',
        'company_id' => $company_c->id,
        'area_id' => $area->id,
        'by_user_id' => $user_c->id,
    ]);
    $project_c->clients()->attach($client_c->id);

    actingAs($admin);

    //* se filtra la información
    $filter = Client::filter([
        'name' => 'Christine Abernathy',
        'projectName' => 'Proyecto X',
        'companyName' => 'Soluciones inter',
    ])->get();

    //* se espera que no retorne nada al no tener coincidencias
    expect($filter)->toBeEmpty();
});
