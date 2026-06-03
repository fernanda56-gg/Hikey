<?php
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;

use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('La vista de olvidaste tu contraseña se muestra correctamente', function () {
    $response = get('/forgot-password');
    $response->assertStatus(200);
});

test('Se puede solicitar el link renovar contraseña', function () {
    Notification::fake(); // Envía una notificación al correo pero es falsa por lo que no se enviará realmente

    /* se genera el usuario */
    $user = User::factory()->create();

    /* Se genera la notificación y se manda al usuario */
    post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class);
});

test('La vista para cambio de contraseña se muestra correctamente', function () {
    Notification::fake();

    $user = User::factory()->create();
    post('/forgot-password', ['email' => $user->email]);

    /* se manda la notificación falsa con el token para ir a la pagina de cambiar contraseña */
    Notification::assertSentTo($user, ResetPassword::class, function($notification) {
        $response = get('/reset-password/'. $notification->token);
        $response->assertStatus(200);
        return true;
    });
});

test('La contraseña puede cambiarse con un token valido', function () {
    Notification::fake();

    $user = User::factory()->create();
    post('/forgot-password', ['email' => $user->email]);

    /* se llenan los datos como el token, correo y contraseña para el cambio de contraseña */
    Notification::assertSentTo($user, ResetPassword::class, function($notification) use ($user) {
        $response = post('/reset-password/', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => '`}2?l6p5w^AD',
            'password_confirmation' => '`}2?l6p5w^AD',
        ]);

        /* se espera que no haya errores al mandar los datos y el usuario sea redirigido al login */
        $response->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

        return true;
    });
});

test('La contraseña no puede cambiarse con token invalido', function () {
    Notification::fake();

    $user = User::factory()->create();
    post('/forgot-password', ['email' => $user->email]);

    /* se llenan los datos como el token, correo y contraseña para el cambio de contraseña */
    Notification::assertSentTo($user, ResetPassword::class, function($notification) use ($user) {
        $response = post('/reset-password/', [
            'token' => 'wrong-token',
            'email' => $user->email,
            'password' => '`}2?l6p5w^AD',
            'password_confirmation' => '`}2?l6p5w^AD',
        ]);

        /* se espera que no haya errores al mandar los datos y el usuario sea redirigido al login */
        $response->assertSessionHasErrors();

        return true;
    });
});
