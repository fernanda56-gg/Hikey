<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\get;

/*
    * ->debug(); abre una pestaña en chrome para que veas el test en vivo
    ! sleep(10); se necesita del timer para poder ver el resultado del test
*/

// * Forma 1 de test de pagina de inicio de sesión
test('Muestra pagina de inicio de sesión', function () {
    $response = get('/');

    $response->assertStatus(200);
});

// * Forma 2 de test de pagina de inicio de sesión
it('Muestra pagina de inicio de sesión', function () {
    visit('/')
    ->assertSee('Inicia sesión en tu cuenta')
    ->assertDontSee('Crear cuenta');
});

it('Muestra pagina de crear cuenta', function () {
    visit('/register')
    ->assertSee('Crear cuenta')
    ->assertDontSee('¿Olvidaste tu contraseña?');
});

it('El usuario puede crear una cuenta en HIKEY', function () {
    /* Se crea al nuevo usuario */
    $role = Role::create(['name' => 'user', 'guard_name' => 'web']); // ! Para generar el usuario se necesito crear el rol USER para este test
    $password = 'Zbz9vvMKO0Ph{';

    $user = User::factory()->make([
        'password' => bcrypt($password),
    ]);
    /* comienza el test */
    visit('/register')
    ->type('name', $user->name)
    ->type('last_name', $user->last_name)
    ->type('email', $user->email)
    ->type('password', $password)
    ->type('password_confirmation', $password)
    ->press('crear cuenta')
    ->assertPathIs('/email/verify');
});

it('El usuario puede iniciar sesión', function () {
    /* Se crea al nuevo usuario */
    $password = 'Ub8)UDud2Cx30';

    $user = User::factory()->create([
        'password' => bcrypt($password)
    ]);

    /* comienza el test */
    visit('/')
    ->type('email', $user->email)
    ->type('password', $password)
    ->press('iniciar sesión')
    ->assertPathIs('/home');
});

it('Prueba de que el menu para móvil funcione', function () {
    /* Se crea al nuevo usuario */
    $password = 'Ub8)UDud2Cx30';

    $user = User::factory()->create([
        'password' => bcrypt($password)
    ]);

    // * Parte 1 de test
    visit('/')
    ->on()->mobile()
    ->type('email', $user->email)
    ->type('password', $password)
    ->press('iniciar sesión')
    ->assertPathIs('/home')
    // * Parte 2 de test
    ->click('[d="M228,128a12,12,0,0,1-12,12H40a12,12,0,0,1,0-24H216A12,12,0,0,1,228,128ZM40,76H216a12,12,0,0,0,0-24H40a12,12,0,0,0,0,24ZM216,180H40a12,12,0,0,0,0,24H216a12,12,0,0,0,0-24Z"]')
    ->assertSee('Proyectos');
});

