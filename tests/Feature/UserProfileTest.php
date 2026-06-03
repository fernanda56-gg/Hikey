<?php

use function Pest\Laravel\{actingAs, get};

use App\Models\User;
use Illuminate\Http\UploadedFile;

// TODO:modificar la función de eliminar cuenta para que el usuario necesite escribir su contraseña para eliminarla lo que esta comentado es para eso.
// TODO: agregar algo para que la contraseña pueda modificarse desde la vista del perfil de usuario y genera un test sobre eso
// ? checa en notas los minutos

test('Se muestra la vista de perfil de usuario', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    actingAs($user);

    $response = get(route('my-account.index', $user));
    $response->assertStatus(200);
});

test('El usuario puede actualizar su información de usuario', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    /* se proporcionan los datos que se van a modificar */
    $response = actingAs($user)
    ->put(route('my-account.edit-account', $user), [
        'name' => 'Shanty',
        'last_name' => 'West',
        'email' => 'sant85@test.com',
    ]);

    $response->assertSessionHasNoErrors()
    ->assertRedirect(route('my-account.index', $user));
    // ! se modificaron las rutas de la función update del user controller para que funcionara el test se quito el back

    /* se actualiza la info del usuario y se espera que sea la misma que se proporciono */
    $user->refresh();
    $this->assertSame('Shanty', $user->name);
    $this->assertSame('West', $user->last_name);
    $this->assertSame('sant85@test.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('El usuario actualiza su información pero el estatus de verificación de correo no cambia', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    $response = actingAs($user)
    ->put(route('my-account.edit-account', [
        'user' => $user->id,
        'name' => 'Shanty',
        'last_name' => 'West',
        'email' => $user->email,
    ]));

    $response->assertSessionHasNoErrors()
    ->assertRedirect(route('my-account.index', $user));

    $user->refresh();

    $this->assertSame('Shanty', $user->name);
    $this->assertSame('West', $user->last_name);
    $this->assertNotNull($user->email_verified_at);
});

test('El usuario puede actualizar su foto de perfil', function () {
    /* se genera el usuario y el espacio en donde simulara almacenar la img */
    Storage::fake('public');

    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    $photo = UploadedFile::fake()->image('avatar.jpg');

    /* se hace la modificación de la img de perfil */
    $response = actingAs($user)
    ->post(route('my-account.profile-photo', $user),[
        'profile-photo' => $photo // * se manda la img por separado en la ruta
        ]);

    $response->assertSessionHasNoErrors();

    /* comprueba que si este almacenada la img en la carpeta public */
    Storage::disk('public')->assertExists('profile-photos/'. $photo->hashName());

    /* comprueba que el usuario tenga la nueva foto en la BD */
    $user->refresh();
    $this->assertNotNull($user->profile_photo);
});

test('El usuario no puede cambiar su foto de perfil si no cumple con los requerimientos', function () {
    Storage::fake('public');

    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    $photo = UploadedFile::fake()->create('avatar.pdf', 100, 'application/pdf')->size(2000);

    /* se modifica la img pero con el formato equivocado */
    $response = actingAs($user)
    ->post(route('my-account.profile-photo', $user),[
        'profile-photo' => $photo // * se manda la img por separado en la ruta
        ]);
    $response->assertSessionHasErrors(['profile-photo']);
});

test('El usuario puede eliminar su cuenta', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    $response = actingAs($user)
    ->delete(route('my-account.delete-account', $user));
    /* ->delete('/my-account/' . $user->id . '/delete', [
        'password' => 'password',
    ]); */

    $response->assertSessionHasNoErrors()
    ->assertRedirect(route('login'));
});


