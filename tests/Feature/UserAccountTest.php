<?php

use function Pest\Laravel\{actingAs, get, seed};

use App\Models\Area;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Spatie\Permission\Models\Role;

test('El usuario con rol ADMIN puede visualizar la pagina de lista de usuarios', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    seed(RolePermissionSeeder::class);
    $user->assignRole('admin');

    $response = actingAs($user)
    ->get(route('manage-account.index'))
    ->assertStatus(200);
});

test('Usuario con cualquier otro rol no puede visualizar la pagina de lista de usuarios', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    seed(RolePermissionSeeder::class);
    $user->assignRole('user');

    $response = actingAs($user)
    ->get(route('manage-account.index'))
    ->assertForbidden(); // * error 403
});

test('El usuario con rol ADMIN puede visualizar la pagina de crear usuario', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    seed(RolePermissionSeeder::class);
    $user->assignRole('admin');

    $response = actingAs($user)
    ->get(route('manage-account.create'))
    ->assertStatus(200);
});

test('Usuario con cualquier otro rol no puede visualizar la pagina de crear usuario', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    seed(RolePermissionSeeder::class);
    $user->assignRole('user');

    $response = actingAs($user)
    ->get(route('manage-account.create'))
    ->assertForbidden(); // * error 403
});

test('Usuario con cualquier otro rol no puede visualizar la pagina de editar usuario', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    $user_x = User::factory()->create();
    seed(RolePermissionSeeder::class);
    $user->assignRole('user');

    $response = actingAs($user)
    ->get(route('manage-account.edit', $user_x))
    ->assertForbidden(); // * error 403
});

test('Usuario con cualquier otro rol no puede eliminar usuarios', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    $user_x = User::factory()->create();
    seed(RolePermissionSeeder::class);
    $user->assignRole('user');

    $response = actingAs($user)
    ->delete(route('manage-account.destroy', $user_x))
    ->assertForbidden(); // * error 403
});

test('El usuario con rol ADMIN puede crear usuarios', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    seed(RolePermissionSeeder::class);
    $user->assignRole('admin');

    // generamos contraseña para usuario
    $password = 'oy84Fh0N{KH1';

    //* obtenemos el rol para el usuario que se va
    $role = Role::where('name', 'user')->first();

    // se crear el usuario
    $response = actingAs($user)
    ->post(route('manage-account.store'), [
        'name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'jane12@example.com',
        'password' => $password,
        'password_confirmation' => $password,
        'roles' => [$role->id], //* asignamos el rol al usuario por el ID
    ]);

    $response->assertSessionHasNoErrors()
    ->assertRedirect(route('manage-account.index'));

    // esperamos que el usuario exista
    expect(User::where('email', 'jane12@example.com', 'password', $password));
});

test('El usuario con rol ADMIN puede editar la info de los usuarios', function () {
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();
    seed(RolePermissionSeeder::class);
    $admin->assignRole('admin');

    //? generamos usuario que se va a modificar
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    //* obtenemos el rol para el usuario que se va
    $role = Role::where('name', 'user')->first();

    //* se edita la info del usuario
    $response = actingAs($user)
    ->put(route('manage-account.update', $user), [
        'name' => $user->name,
        'last_name' => $user->last_name,
        'email' => 'janedoe@example.com',
        'password' => $user->password,
        'password_confirmation' => $user->password,
        'roles' => [$role->id],
    ]);

    $response->assertSessionHasNoErrors()
    ->assertRedirect(route('manage-account.index'));
    $user->refresh();

    // esperamos que el usuario exista
    expect(User::where('email', 'janedoe@example.com', 'password', $user->password));
});

test('El usuario con rol ADMIN puede hacer soft delete y restaurar usuario', function () {
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();
    seed(RolePermissionSeeder::class);
    $admin->assignRole('admin');

    //? generamos usuario que se va a modificar
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    $response = actingAs($admin)
    ->delete(route('manage-account.destroy', $user));

    $this->assertSoftDeleted('users', ['id' => $user->id]);
    $user->restore();
    $user->refresh();
    expect($user->deleted_at)->toBeNull();
});

test('El usuario con rol ADMIN puede hacer soft delete y eliminar definitivamente el usuario', function () {
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();
    seed(RolePermissionSeeder::class);
    $admin->assignRole('admin');

    //? generamos usuario que se va a modificar
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    $response = actingAs($admin)
    ->delete(route('manage-account.destroy', $user));

    $this->assertSoftDeleted('users', ['id' => $user->id]);
    $user->forceDelete();
    $user->refresh();
    expect(User::find($user->id))->toBeNull();
});

test('El usuario con rol ADMIN puede filtrar por nombre de usuario', function () {
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();
    seed(RolePermissionSeeder::class);
    $admin->assignRole('admin');

    //? generamos usuarios para filtros
    /** @var \App\Models\User $user */
    $user_a = User::factory()->create([
        'name' => 'Joana',
        'last_name' => 'Homenick',
    ]);

    $user_b = User::factory()->create([
        'name' => 'Grady',
        'last_name' => 'Purdy',
    ]);

    actingAs($admin);

    $filter = User::filter([
        'name' => 'Joana',
        'last_name' => 'Homenick',
    ])->get();

    expect($filter)->toHaveCount(1)
    ->and($filter->first()->id)->toBe($user_a->id);
});

test('El usuario con rol ADMIN puede filtrar por rol de usuario', function () {
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();
    seed(RolePermissionSeeder::class);
    $admin->assignRole('admin');

    //? generamos usuarios para filtros
    /** @var \App\Models\User $user */
    $user_a = User::factory()->create();
    $user_a->assignRole('manager');

    $user_b = User::factory()->create();
    $user_b->assignRole('team-leader');

    $user_c = User::factory()->create();
    $user_c->assignRole('user');

    actingAs($admin);

    $filter = User::filter([
        'role' => 'team-leader'
    ])->get();

    expect($filter)->toHaveCount(1)
    ->and($filter->first()->id)->toBe($user_b->id);
});

test('El usuario con rol ADMIN no introduce nada en los filtros y retorna todos los registros', function () {
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();
    seed(RolePermissionSeeder::class);
    $admin->assignRole('admin');

    //? generamos usuarios para filtros
    /** @var \App\Models\User $user */
    $user_a = User::factory()->create([
        'name' => 'Joana',
        'last_name' => 'Homenick',
    ]);
    $user_a->assignRole('manager');

    $user_b = User::factory()->create([
        'name' => 'Grady',
        'last_name' => 'Purdy',
    ]);
    $user_b->assignRole('team-leader');

    $user_c = User::factory()->create([
        'name' => 'Jared',
        'last_name' => 'Bayer',
    ]);
    $user_c->assignRole('user');

    actingAs($admin);

    $filter = User::filter([
        'name' => '',
        'last_name' => '',
        'role' => '',
    ])->where('id', '!=', $admin->id) //? excluimos al admin en el filtrado
    ->get();

    expect($filter)->toHaveCount(3);
});

test('El usuario con rol ADMIN utiliza ambos filtros', function () {
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();
    seed(RolePermissionSeeder::class);
    $admin->assignRole('admin');

    //? generamos usuarios para filtros
    /** @var \App\Models\User $user */
    $user_a = User::factory()->create([
        'name' => 'Joana',
        'last_name' => 'Homenick',
    ]);
    $user_a->assignRole('manager');

    $user_b = User::factory()->create([
        'name' => 'Grady',
        'last_name' => 'Purdy',
    ]);
    $user_b->assignRole('team-leader');

    $user_c = User::factory()->create([
        'name' => 'Jared',
        'last_name' => 'Bayer',
    ]);
    $user_c->assignRole('user');

    actingAs($admin);

    $filter = User::filter([
        'name' => 'Jared',
        'last_name' => 'Bayer',
        'role' => 'user',
    ])->where('id', '!=', $admin->id) //? excluimos al admin en el filtrado
    ->get();

    expect($filter)->toHaveCount(1)
    ->and($filter->first()->id)->toBe($user_c->id);
});

test('El filtro no retorna nada si no hay coincidencias', function () {
    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();
    seed(RolePermissionSeeder::class);
    $admin->assignRole('admin');

    //? generamos usuarios para filtros
    /** @var \App\Models\User $user */
    $user_a = User::factory()->create([
        'name' => 'Joana',
        'last_name' => 'Homenick',
    ]);
    $user_a->assignRole('manager');

    $user_b = User::factory()->create([
        'name' => 'Grady',
        'last_name' => 'Purdy',
    ]);
    $user_b->assignRole('team-leader');

    $user_c = User::factory()->create([
        'name' => 'Jared',
        'last_name' => 'Bayer',
    ]);
    $user_c->assignRole('user');

    actingAs($admin);

    $filter = User::filter([
        'name' => 'Christine',
        'last_name' => 'Abernathy',
        'role' => 'user',
    ])->where('id', '!=', $admin->id) //? excluimos al admin en el filtrado
    ->get();

    //* se espera que no retorne nada al no tener coincidencias
    expect($filter)->toBeEmpty();
});
