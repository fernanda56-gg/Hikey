<?php

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\{actingAs, get};

test('Se visualiza el mensaje de verificación de correo', function () {
    /* se genera el usuario */
    $user = User::factory()->unverified()->create();

    /* comienza el test */
    actingAs($user);

    get(route('verification.notice'))
    ->assertStatus(200);
});

test('El correo puede verificarse', function () {
    /* se genera el usuario */
    $user = User::factory()->unverified()->create();

    // * se genera un fake event este se puede usar para notificaciones, correos, etc.
    Event::fake();

    /* se genera url de link de verificación */
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    /* el usuario accede al link de verificación */
    $response = actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class); // El correo es verificado se lanza el evento
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue(); //se espera que el registro del usuario ya indique que ha sido verificado
    $response->assertRedirect(route('inicio', absolute:false))->assertSessionHas('success', 'Correo verificado'); //redirige al dashboard y muestra la alerta de correo verificado
});

test('El correo no es verificado por ruta invalida', function () {
    /* se genera el usuario */
    $user = User::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')]
    );

    actingAs($user)->get($verificationUrl);

    //* Al darle el correo equivocado se espera que el usuario no pueda verificar
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});
