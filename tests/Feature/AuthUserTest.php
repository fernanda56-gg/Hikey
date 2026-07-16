<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\{actingAs, delete, get, post, seed};

/*
    * ->debug(); abre una pestaña en chrome para que veas el test en vivo
    ! sleep(10); se necesita del timer para poder ver el resultado del test
    * assertNoSmoke() es lo mismo que assertNoJavascriptErrors() y assertNoConsoleLogs()
*/

// ? Forma 1 de test de pagina de inicio de sesión
test('Muestra pagina de inicio de sesión', function () {
    $response = get('/');

    $response->assertStatus(200);
});

test('Muestra pagina de crear cuenta', function () {
    $response = get(route('register'));

    $response->assertStatus(200);
    $response->assertDontSee('¿Olvidaste tu contraseña?');
});

test('El usuario puede crear una cuenta en HIKEY', function () {
    /* Se crea al nuevo usuario */
    $role = Role::create(['name' => 'user', 'guard_name' => 'web']); // ! Para generar el usuario se necesito crear el rol USER para este test
    $password = 'Zbz9vvMKO0Ph{';

    $user = User::factory()->make([
        'password' => bcrypt($password),
    ]);

    $response = actingAs($user)
    ->post(route('register.store'), [
        'name' => $user->name,
        'last_name' => $user->last_name,
        'email' => $user->email,
        'password' => $password,
        'password_confirmation' => $password
    ]);

    $response->assertSessionHasNoErrors()
    ->assertRedirect(route('inicio'));
});

test('El usuario puede iniciar sesión en HIKEY', function () {
    // se crear el nuevo usuario
    $password = 'Ub8)UDud2Cx30';

    $user = User::factory()->create([
        'password' => bcrypt($password)
    ]);

    $response = actingAs($user)
    ->post(route('login.store'), [
        'email' => $user->email,
        'password' => $password
    ]);

    $response->assertSessionHasNoErrors()
    ->assertRedirect(route('inicio'));
});

test('El usuario no puede iniciar sesión por credenciales incorrectas', function () {
    //información de usuario
    $password = 'wrong-password';
    $email = 'wrong@example.com';

    $user = User::factory()->create();

    $response = actingAs($user)
    ->post(route('login.store'), [
        'email' => $email,
        'password' => $password
    ]);

    $response->assertSessionHasErrors();
});

test('El usuario puede cerrar sesión', function () {
    // se crear el nuevo usuario
    $password = 'Ub8)UDud2Cx30';

    $user = User::factory()->create([
        'password' => bcrypt($password)
    ]);

    $response = actingAs($user)
    ->post(route('login.store'), [
        'email' => $user->email,
        'password' => $password
    ]);

    $response->assertSessionHasNoErrors()
    ->assertRedirect(route('inicio'));

    $response = delete(route('logout'))
    ->assertRedirect(route('login'));
});
