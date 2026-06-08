<?php

use App\Models\User;
use App\Models\Company;
use Database\Seeders\RolePermissionSeeder;

use function Pest\Laravel\{actingAs, get, seed};

test('El usuario puede visualizar la pagina de empresa a pesar de no formar parte de una', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    seed(RolePermissionSeeder::class);
    $user->assignRole('user');

    actingAs($user);
    $response = get(route('companies.index'));
    $response->assertStatus(200);
});

test('El usuario puede visualizar la pagina de crear empresa', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    seed(RolePermissionSeeder::class);
    $user->assignRole('user');

    actingAs($user);
    $response = get(route('companies.create'));
    $response->assertStatus(200);
});

test('El usuario puede visualizar la pagina de empresa', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    $company = Company::factory()->create([
        'owner_id' => $user->id,
    ]);
    $user->companies()->attach($company->id); // vinculamos al usuario con la empresa

    actingAs($user);
    $response = get(route('companies.show', $company));
    $response->assertStatus(200);
});

test('El usuario puede crear una empresa', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    // se ejecuta el seeder y se le asigna el rol USER al usuario
    seed(RolePermissionSeeder::class);
    $user->assignRole('user');

    // se crea la empresa
    $response = actingAs($user)
    ->post(route('companies.store'), [
        'name' => 'NovaTech Soluciones Integrales',
        'email' => 'contacto@novatechsi.com',
        'address' => 'Av. Vallarta 1234, Col. Americana, C.P. 44160',
        'city' => 'Guadalajara',
        'country' => 'México',
        'phone' => '+52 33 2456 7890',
        'web_address' => 'https://www.novatech.co.uk',
        'tax_id' => '85-51498',
        'owner_id' => $user->id,
    ]);

    // * obtenemos la info de la empresa que acabamos de crear
    $company = Company::latest()->first();

    /* esperamos que no haya errores y sea redirigido a la pagina de la empresa */
    $response->assertSessionHasNoErrors()
    ->assertRedirect(route('companies.show', $company));

    //* se refresca el registro del usuario se espera que su rol ahora sea MANAGER
    $user->refresh();
    expect($user->hasRole('manager'))->toBeTrue();
});

test('El usuario puede editar la información de la empresa', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    seed(RolePermissionSeeder::class);
    $user->assignRole('manager');

    $company = Company::factory()->create([
        'owner_id' => $user->id,
    ]);
    $user->companies()->attach($company->id); // vinculamos al usuario con la empresa

    $response = actingAs($user)
    ->put(route('companies.update', $company), [
        'name' => 'Empresa X',
        'email' => $company->email,
        'address' => $company->address,
        'city' => 'London',
        'country' => 'United Kingdom',
        'phone' => $company->phone,
        'web_address' => $company->web_address,
        'tax_id' => $company->tax_id,
        'company_code' => $company->company_code,
        'owner_id' => $company->owner_id,
    ]);

    $response->assertSessionHasNoErrors()
    ->assertRedirect(route('companies.show', $company));

    $this->assertDatabaseHas('companies', [
        'id' => $company->id,
        'name' => 'Empresa X',
        'city' => 'London',
        'country' => 'United Kingdom',
    ]);
});

test('El usuario puede eliminar su empresa', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    seed(RolePermissionSeeder::class);
    $user->assignRole('manager');

    $company = Company::factory()->create([
        'owner_id' => $user->id,
    ]);
    $user->companies()->attach($company->id); // vinculamos al usuario con la empresa

    //se elimina la empresa
    $response = actingAs($user)
    ->delete(route('companies.destroy', $company));

    //* se espera que no haya errores y que redirija al index
    $response->assertSessionHasNoErrors()
    ->assertRedirect(route('companies.index'));

    //* buscara el id de la empresa pero como se elimino esperamos que no lo encuentre
    expect(Company::find($company->id))->toBeNull();

    //* al eliminar la empresa se espera que el rol del usuario sea otra vez USER
    $user->refresh();
    expect($user->hasRole('user'))->toBeTrue();
});

test('El usuario puede visualizar la pagina de miembros de la empresa', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    $company = Company::factory()->create([
        'owner_id' => $user->id,
    ]);
    $user->companies()->attach($company->id); // vinculamos al usuario con la empresa

    actingAs($user);
    $response = get(route('companies.listMember', $company))
    ->assertStatus(200);
});

test('El usuario puede sacar a miembros de la empresa', function () {
    /** @var \App\Models\User $owner, $member */
    $owner =  User::factory()->create();
    $member = User::factory()->create();

    //se asignan roles para ambos usuarios
    seed(RolePermissionSeeder::class);
    $owner->assignRole('manager');
    $member->assignRole('user');

    // se vinculan los usuarios con la empresa
    $company = Company::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $owner->companies()->attach($company->id);
    $member->companies()->attach($company->id);

    //*sacamos al usuario member de la empresa
    $response = actingAs($owner)
    ->delete(route('companies.leave', [$company, $member]));

    // esperamos que no haya errores y redirija nuevamente a la lista de miembros
    $response->assertSessionHasNoErrors()
    ->assertRedirect(route('companies.listMember', $company));

    $company->refresh();
    expect($company->member()->where('user_id', $member->id)->exists())->toBeFalse(); //* verificamos que el usuario haya sido eliminado de la empresa
    expect($company->member()->where('user_id', $owner->id)->exists())->toBeTrue(); //* nos aseguramos que el dueño aun este en la empresa
});

test('El dueño de la empresa no puede salir de la empresa', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    seed(RolePermissionSeeder::class);
    $user->assignRole('manager');

    $company = Company::factory()->create([
        'owner_id' => $user->id,
    ]);
    $user->companies()->attach($company->id); // vinculamos al usuario con la empresa

    // se elimina al dueño de la empresa
    $response = actingAs($user)
    ->delete(route('companies.leave', [$company, $user]));

    //* se espera que no pueda eliminar al dueño y por lo tanto de error
    $response->assertRedirect(route('companies.show', $company))
    ->assertSessionHas('error');

    //* se espera que el dueño este aún en la empresa
    $company->refresh();
    expect($company->member()->where('user_id', $user->id)->exists())->toBeTrue();
});

test('El usuario puede mandar correo con código de invitación a otros usuarios', function () {
    //* se generan los 2 usuarios
    /** @var \App\Models\User $owner */
    $owner =  User::factory()->create();

    /** @var \App\Models\User $user */
    $user = User::factory()->create([
        'email' => 'example@test.com'
    ]);

    //se asignan roles
    seed(RolePermissionSeeder::class);
    $owner->assignRole('manager');

    // se vincula al usuario owner con la empresa
    $company = Company::factory()->create([
        'owner_id' => $owner->id,
        'company_code' => 'ABCD1234',
    ]);
    $owner->companies()->attach($company->id);

    $response = actingAs($owner)
    ->post(route('companies.sendInvitation', $company), [
        'email' => 'example@test.com',
    ]);

    $response->assertSessionHasNoErrors()
    ->assertRedirect(route('companies.show', $company));
});

test('El usuario no puede mandar correo con código de invitación por no tener los permisos necesarios', function () {
    //* se generan los 2 usuarios
    /** @var \App\Models\User $owner */
    $owner =  User::factory()->create();

    /** @var \App\Models\User $member */
    $member =  User::factory()->create();

    /** @var \App\Models\User $user */
    $user = User::factory()->create([
        'email' => 'example@test.com'
    ]);

    //se asignan roles
    seed(RolePermissionSeeder::class);
    $owner->assignRole('manager');
    $member->assignRole('user');

    // se vincula al usuario owner con la empresa
    $company = Company::factory()->create([
        'owner_id' => $owner->id,
        'company_code' => 'ABCD1234',
    ]);
    $owner->companies()->attach($company->id);

    $response = actingAs($member)
    ->post(route('companies.sendInvitation', $company), [
        'email' => 'example@test.com',
    ]);

    $response->assertForbidden();
});



test('El usuario puede unirse a la empresa', function () {
    //* se generan los 2 usuarios
    /** @var \App\Models\User $owner */
    $owner =  User::factory()->create();
    /** @var \App\Models\User $member */
    $member = User::factory()->create();

    //se asignan roles para ambos usuarios
    seed(RolePermissionSeeder::class);
    $owner->assignRole('manager');
    $member->assignRole('user');

    // se vinculan los usuarios con la empresa
    $company = Company::factory()->create([
        'owner_id' => $owner->id,
        'company_code' => 'ABCD1234',
    ]);
    $owner->companies()->attach($company->id);

    //* el usuario se une a la empresa
    $response = actingAs($member)
    ->post(route('companies.checkCode'), [
        'code' => 'ABCD1234',
    ]);

    $response->assertSessionHasNoErrors()
    ->assertRedirect(route('companies.show', $company));

    //* esperamos que el usuario ya forme parte de la empresa y que tenga el rol correcto que es MIEMBRO
    $company->refresh();
    expect($company->member()->where('user_id', $member->id)->exists())->toBeTrue();

    expect($company->member()
        ->where('user_id', $member->id)
        ->wherePivot('role', 'miembro')
        ->exists()
    )->toBeTrue();
});

test('El usuario no puede unirse a la empresa con el código incorrecto', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    seed(RolePermissionSeeder::class);
    $user->assignRole('user');

    $response = actingAs($user)
    ->post(route('companies.checkCode'), [
        'code' => 'WRONG56789',
    ]);

    $response->assertSessionHasErrors(['code']);
});

test('El usuario con rol de ADMIN puede visualizar el listado de empresas', function () {
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();

    /** @var \App\Models\User $owner */
    $owner = User::factory()->create();

    seed(RolePermissionSeeder::class);
    $admin->assignRole('admin');
    $owner->assignRole('manager');

    $companies = Company::factory()->count(5)->create([
        'owner_id' => $owner->id,
    ]);
    $owner->companies()->attach($companies->pluck('id')); // se vinculan las 5 empresas al mismo user

    $response = actingAs($admin)
    ->get(route('companies.index'));

    $response->assertStatus(200);
});

test('El usuario con rol ADMIN puede ver una empresa en especifico', function () {
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();

    /** @var \App\Models\User $owner */
    $owner = User::factory()->create();

    seed(RolePermissionSeeder::class);
    $admin->assignRole('admin');
    $owner->assignRole('manager');

    $company = Company::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $owner->companies()->attach($company->id);

    $response = actingAs($admin)
    ->get(route('companies.show', $company));

    $response->assertStatus(200);
});

test('El usuario ADMIN puede filtrar por nombre a las empresas', function () {
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();

    /** @var \App\Models\User $owner */
    $owner = User::factory()->create();

    seed(RolePermissionSeeder::class);
    $admin->assignRole('admin');
    $owner->assignRole('manager');

    $companyA = Company::factory()->create([
        'name' => 'Empresa Alpha',
        'owner_id' => $owner->id,
    ]);
    $owner->companies()->attach($companyA->id);

    $companyB = Company::factory()->create([
        'name' => 'Empresa Beta',
        'owner_id' => $owner->id,
    ]);
    $owner->companies()->attach($companyB->id);

    actingAs($admin);

    // * se filtra la información por el nombre
    $filter = Company::filter(['name' => 'Empresa Alpha'])->get();

    // * se espera que solo haya un resultado
    expect($filter)->toHaveCount(1)
    ->and($filter->first()->id)->toBe($companyA->id);
});

test('El usuario ADMIN puede filtrar por la ciudad de las empresas', function () {
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();

    /** @var \App\Models\User $owner */
    $owner = User::factory()->create();

    seed(RolePermissionSeeder::class);
    $admin->assignRole('admin');
    $owner->assignRole('manager');

    $companyA = Company::factory()->create([
        'city' => 'Monterrey',
        'owner_id' => $owner->id,
    ]);
    $owner->companies()->attach($companyA->id);

    $companyB = Company::factory()->create([
        'city' => 'Quebec',
        'owner_id' => $owner->id,
    ]);
    $owner->companies()->attach($companyB->id);

    actingAs($admin);

    // * se filtra la información por ciudad
    $filter = Company::filter(['city' => 'Quebec'])->get();

    // * se espera que solo haya un resultado
    expect($filter)->toHaveCount(1)
    ->and($filter->first()->id)->toBe($companyB->id);
});

test('El usuario ADMIN puede filtrar por el país de las empresas', function () {
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();

    /** @var \App\Models\User $owner */
    $owner = User::factory()->create();

    seed(RolePermissionSeeder::class);
    $admin->assignRole('admin');
    $owner->assignRole('manager');

    $companyA = Company::factory()->create([
        'country' => 'Brazil',
        'owner_id' => $owner->id,
    ]);
    $owner->companies()->attach($companyA->id);

    $companyB = Company::factory()->create([
        'country' => 'México',
        'owner_id' => $owner->id,
    ]);
    $owner->companies()->attach($companyB->id);

    actingAs($admin);

    // * se filtra la información por país
    $filter = Company::filter(['country' => 'Brazil'])->get();

    // * se espera que solo haya un resultado
    expect($filter)->toHaveCount(1)
    ->and($filter->first()->id)->toBe($companyA->id);
});

test('El usuario ADMIN no coloca nada en los filtros y retorna todos los registros', function () {
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();

    /** @var \App\Models\User $owner */
    $owner = User::factory()->create();

    seed(RolePermissionSeeder::class);
    $admin->assignRole('admin');
    $owner->assignRole('manager');

    $companyA = Company::factory()->create([
        'name' => 'Empresa X',
        'city' => 'Sao Paulo',
        'country' => 'Brazil',
        'owner_id' => $owner->id,
    ]);
    $owner->companies()->attach($companyA->id);

    $companyB = Company::factory()->create([
        'name' => 'Empresa Y',
        'city' => 'Guadalajara',
        'country' => 'México',
        'owner_id' => $owner->id,
    ]);
    $owner->companies()->attach($companyB->id);

    actingAs($admin);

    // * se filtra la información
    $filter = Company::filter([
        'name' => '',
        'city' => '',
        'country' => ''
    ])->get();

    //* se espera que retorne ambos registros
    expect($filter)->toHaveCount(2);
});

test('El usuario ADMIN utiliza los 3 filtros y da un registro en especifico', function () {
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();

    /** @var \App\Models\User $owner */
    $owner = User::factory()->create();

    seed(RolePermissionSeeder::class);
    $admin->assignRole('admin');
    $owner->assignRole('manager');

    $companyA = Company::factory()->create([
        'name' => 'Empresa X',
        'city' => 'Sao Paulo',
        'country' => 'Brazil',
        'owner_id' => $owner->id,
    ]);
    $owner->companies()->attach($companyA->id);

    $companyB = Company::factory()->create([
        'name' => 'Empresa Y',
        'city' => 'Guadalajara',
        'country' => 'México',
        'owner_id' => $owner->id,
    ]);
    $owner->companies()->attach($companyB->id);

    actingAs($admin);

    // * se filtra la información
    $filter = Company::filter([
        'name' => 'Empresa Y',
        'city' => 'Guadalajara',
        'country' => 'México'
    ])->get();

    // * se espera que solo haya un resultado
    expect($filter)->toHaveCount(1)
    ->and($filter->first()->id)->toBe($companyB->id);
});

test('El filtro no retorna nada si no hay coincidencias', function () {
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();

    /** @var \App\Models\User $owner */
    $owner = User::factory()->create();

    seed(RolePermissionSeeder::class);
    $admin->assignRole('admin');
    $owner->assignRole('manager');

    $companyA = Company::factory()->create([
        'name' => 'Empresa X',
        'city' => 'Sao Paulo',
        'country' => 'Brazil',
        'owner_id' => $owner->id,
    ]);
    $owner->companies()->attach($companyA->id);

    $companyB = Company::factory()->create([
        'name' => 'Empresa Y',
        'city' => 'Guadalajara',
        'country' => 'México',
        'owner_id' => $owner->id,
    ]);
    $owner->companies()->attach($companyB->id);

    actingAs($admin);

    // * se filtra la información
    $filter = Company::filter([
        'name' => 'Empresa A',
        'city' => 'Valencia',
        'country' => 'España'
    ])->get();

    //* se espera que no retorne nada al no tener coincidencias
    expect($filter)->toBeEmpty();
});

