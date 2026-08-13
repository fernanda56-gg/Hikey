<?php

use function Pest\Laravel\{actingAs, get, seed};

use App\Models\Area;
use App\Models\Company;
use App\Models\User;
use App\Models\Project;
use Database\Seeders\AreaSeeder;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;


test('El usuario puede visualizar la vista de proyectos', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    actingAs($user);

    $response = get(route('projects.index'));
    $response->assertStatus(200);
});

test('El usuario puede visualizar la vista de crear proyectos', function () {
    /* se crea el rol de usuario */
    Role::create(['name' => 'admin']);

    /* se genera el usuario */
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    $user->assignRole('admin');

    actingAs($user);
    $response = get(route('projects.create'));
    $response->assertStatus(200);
});


test('El usuario puede crear proyectos', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    /* se crea una compañía, asignamos que el dueño de la empresa es el usuario creado */
    $company = Company::factory()->create([
        'owner_id' => $user->id,
    ]);
    $user->companies()->attach($company->id); // vinculamos al usuario con la empresa

    /* agregamos el seeder de area y sacamos el id del primer registro del seed */
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    /* llenamos los campos del form de proyectos */
    $response = actingAs($user)
    ->post(route('projects.store'), [
        'name' => 'Empresa X',
        'description' => 'Empresa de mercadotecnia',
        'link' => 'https://example.com/proyecto-x',
        'image_path' => 'https://example.com/proyecto-img',
        'start_date' => now()->addDays(7)->toDateString(),
        'end_date' => now()->addDays(14)->toDateString(),
        'by_user_id' => $user->id,
        'area_id' => $area->id,
        'company_id' => $company->id,
    ]);

    /* esperamos que el proyecto no tenga errores y sea redirigido al index */
    $response->assertSessionHasNoErrors()
    ->assertRedirect(route('projects.index'));

    expect(Project::where('name', 'Empresa X')->where('by_user_id', $user->id)->exists())->toBeTrue();
});

test('El usuario puede visualizar un proyecto en especifico', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    /* se crea la empresa, el usuario creado es el dueño de la empresa */
    $company = Company::factory()->create([
        'owner_id' => $user->id,
    ]);
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

    actingAs($user);
    get(route('projects.show', $project))
    ->assertOk()
    ->assertSee($project->name)
    ->assertSee($project->description);
});

test('El usuario no puede editar el proyecto si no es dueño de este', function () {
    /* se crean los roles para usuarios */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* se genera al usuario dueño del proyecto y al usuario normal */
    $owner = User::factory()->create();

    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    /* se crea la empresa, el usuario creado es el dueño de la empresa */
    $company = Company::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $owner->companies()->attach($company->id);

    /* se ejecuta el seed de areas */
    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    /* se genera el proyecto */
    $project = Project::factory()->create([
        'by_user_id' => $owner->id,
        'area_id' => $area,
        'company_id' => $company->id,
    ]);

    actingAs($user);
    get(route('projects.edit', $project))->assertForbidden(); // ! se espera que de el error 403 al no ser dueño del proyecto
});


test('El usuario puede editar proyectos', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    /* se crea la empresa, el usuario creado es el dueño de la empresa */
    $company = Company::factory()->create([
        'owner_id' => $user->id,
    ]);
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

    $response = actingAs($user)
    ->put(route('projects.update', $project), [
        'name' => 'Titulo nuevo',
        'description' => 'Sinopsis nueva',
        'link' => $project->link,
        'image_path' => $project->image_path,
        'start_date' => $project->start_date,
        'end_date' => $project->end_date,
        'by_user_id' => $user->id,
        'area_id' => $area->id,
        'company_id' => $company->id,
    ]);

    $response->assertSessionHasNoErrors()
    ->assertRedirect(route('projects.show', $project));

    expect($project->fresh())
    ->name->toBe('Titulo nuevo')
    ->description->toBe('Sinopsis nueva');
});

test('El usuario puede hacer soft delete y restaurar el proyecto', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    /* se crea la empresa, el usuario creado es el dueño de la empresa */
    $company = Company::factory()->create([
        'owner_id' => $user->id,
    ]);
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

    //* Se elimina el proyecto y aseguramos que este en el soft delete
    $project->delete();
    /* $this->assertSoftDeleted('projects', ['id' => $project->id]); */
    expect($project->fresh()->deleted_at)->not->toBeNull(); // ? alternativa a assertSoftDeleted

    //* Restauramos el proyecto, refrescamos la BD y esperamos que el timestamp de deleted_at este en NULL
    $project->restore();
    $project->refresh();
    expect($project->deleted_at)->toBeNull();

});

test('El usuario puede hacer soft delete y eliminar definitivamente', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    /* se crea la empresa, el usuario creado es el dueño de la empresa */
    $company = Company::factory()->create([
        'owner_id' => $user->id,
    ]);
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

    //* Se elimina el proyecto y aseguramos que este en el soft delete
    $project->delete();
    expect($project->fresh()->deleted_at)->not->toBeNull();

    //* Forzamos la eliminación del proyecto, refrescamos la BD y aseguramos que ya no este el proyecto en la tabla
    $project->forceDelete();
    $project->refresh();

    /* $this->assertDatabaseMissing('projects', ['id' => $project->id] ); */
    expect(Project::find($project->id))->toBeNull(); // ? alternativa del assertDatabaseMissing
    expect(Project::withTrashed()->find($project->id))->toBeNull();

});

test('El usuario puede filtrar los proyectos por nombre', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    /* se crea la empresa, el usuario creado es el dueño de la empresa */
    $company = Company::factory()->create([
        'owner_id' => $user->id,
    ]);
    $user->companies()->attach($company->id);

    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    //* se crean dos registros para ejecutar el filtro
    $option_a = Project::factory()->create([
        'name' => 'Técnicas de gestión laboral',
        'by_user_id' => $user->id,
        'area_id' => $area,
        'company_id' => $company->id,
    ]);

    $option_b = Project::factory()->create([
        'name' => 'Migración de bases de datos',
        'by_user_id' => $user->id,
        'area_id' => $area,
        'company_id' => $company->id,
    ]);

    // * se filtra la información por el nombre
    $filter = Project::filter(['name' => 'datos'])->get();

    // * se espera que solo haya un resultado
    expect($filter)->toHaveCount(1)
    ->and($filter->first()->id)->toBe($option_b->id);
});

test('El usuario puede filtrar los proyectos por area', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    /* se crea la empresa, el usuario creado es el dueño de la empresa */
    $company = Company::factory()->create([
        'owner_id' => $user->id,
    ]);
    $user->companies()->attach($company->id);

    seed(AreaSeeder::class);
    $area_x = Area::first();
    $area_y = Area::skip(1)->first();

    Project::factory(2)->create([
        'by_user_id' => $user->id,
        'area_id' => $area_x,
        'company_id' => $company->id,
    ]);

    Project::factory(1)->create([
        'by_user_id' => $user->id,
        'area_id' => $area_y,
        'company_id' => $company->id,
    ]);

    //* se filtra la info por area
    $filter = Project::filter(['area' => $area_x->id])->get();

    // * se espera que haya 2 resultados
    expect($filter)->toHaveCount(2)
    ->and($filter->every(fn($p) => $p->area_id === $area_x->id))->toBeTrue();
});

test('El usuario puede filtrar los proyectos por su estatus', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    /* se crea la empresa, el usuario creado es el dueño de la empresa */
    $company = Company::factory()->create([
        'owner_id' => $user->id,
    ]);
    $user->companies()->attach($company->id);

    seed(AreaSeeder::class);
    $area = Area::limit(1)->first();

    //* se crean dos registros para ejecutar el filtro
    $option_a = Project::factory()->create([
    'start_date' => null,
    'end_date'   => null,
    'status'     => 'Pendiente',
    'by_user_id' => $user->id,
    'area_id'    => $area->id,
    'company_id' => $company->id,
    ]);

    $option_b = Project::factory()->create([
        'start_date' => now()->addDays(7)->toDateString(),
        'end_date'   => null,
        'status'     => 'En progreso',
        'by_user_id' => $user->id,
        'area_id'    => $area->id,
        'company_id' => $company->id,
    ]);

    $option_c = Project::factory()->create([
        'start_date' => now()->addDays(7)->toDateString(),
        'end_date'   => now()->addDays(14)->toDateString(),
        'status'     => 'Completado',
        'by_user_id' => $user->id,
        'area_id'    => $area->id,
        'company_id' => $company->id,
    ]);

    //* se filtra la info por status
    $filter = Project::filter(['status' => 'Pendiente'])->get();

    // * se espera que solo haya un resultado
    expect($filter)->toHaveCount(1)
        ->and($filter->first()->id)->toBe($option_a->id);
});

test('El usuario no coloca nada en los filtros y retorna todos los registros', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    /* se crea la empresa, el usuario creado es el dueño de la empresa */
    $company = Company::factory()->create([
        'owner_id' => $user->id,
    ]);
    $user->companies()->attach($company->id);

    seed(AreaSeeder::class);
    $area_x = Area::first();
    $area_y = Area::skip(1)->first();

    //* se crean dos registros para ejecutar el filtro
    $option_a = Project::factory()->create([
    'name' => 'Facturas fiscales',
    'start_date' => null,
    'end_date'   => null,
    'status'     => 'Pendiente',
    'by_user_id' => $user->id,
    'area_id'    => $area_y->id,
    'company_id' => $company->id,
    ]);

    $option_b = Project::factory()->create([
        'name' => 'Presentación del proyecto',
        'start_date' => now()->addDays(7)->toDateString(),
        'end_date'   => null,
        'status'     => 'En progreso',
        'by_user_id' => $user->id,
        'area_id'    => $area_x->id,
        'company_id' => $company->id,
    ]);

    $option_c = Project::factory()->create([
        'name' => 'Campaña de publicidad',
        'start_date' => now()->addDays(7)->toDateString(),
        'end_date'   => now()->addDays(14)->toDateString(),
        'status'     => 'Completado',
        'by_user_id' => $user->id,
        'area_id'    => $area_x->id,
        'company_id' => $company->id,
    ]);

    //? no se coloca nada en los filtros
    $filter = Project::filter([
        'name' => '',
        'status' => '',
        'area' => '',
    ])->get();

    //* se espera que retorne los 3 registros
    expect($filter)->toHaveCount(3);
});

test('El usuario utiliza los 3 filtros y da un registro en especifico', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    /* se crea la empresa, el usuario creado es el dueño de la empresa */
    $company = Company::factory()->create([
        'owner_id' => $user->id,
    ]);
    $user->companies()->attach($company->id);

    seed(AreaSeeder::class);
    $area_x = Area::first();
    $area_y = Area::skip(1)->first();

    //* se crean dos registros para ejecutar el filtro
    $option_a = Project::factory()->create([
    'name' => 'Facturas fiscales',
    'start_date' => null,
    'end_date'   => null,
    'status'     => 'Pendiente',
    'by_user_id' => $user->id,
    'area_id'    => $area_y->id,
    'company_id' => $company->id,
    ]);

    $option_b = Project::factory()->create([
        'name' => 'Presentación del proyecto',
        'start_date' => now()->addDays(7)->toDateString(),
        'end_date'   => null,
        'status'     => 'En progreso',
        'by_user_id' => $user->id,
        'area_id'    => $area_x->id,
        'company_id' => $company->id,
    ]);

    $option_c = Project::factory()->create([
        'name' => 'Campaña de publicidad',
        'start_date' => now()->addDays(7)->toDateString(),
        'end_date'   => now()->addDays(14)->toDateString(),
        'status'     => 'Completado',
        'by_user_id' => $user->id,
        'area_id'    => $area_x->id,
        'company_id' => $company->id,
    ]);

    // ! se colocan los datos de un proyecto en especifico
    $filter = Project::filter([
        'name' => 'Campaña de publicidad',
        'status' => 'Completado',
        'area' => $area_x->id,
    ])->get();

    //* se espera que retorne un resultado y se comprueba que sea la misma info
    expect($filter)->toHaveCount(1)
    ->and($filter->first()->id)->toBe($option_c->id);
});

test('El filtro no retorna nada si no hay coincidencias con la información', function () {
    /* se crean los roles para usuario */
    Role::create(['name' => 'user']);
    Role::create(['name' => 'manager']);

    /* el usuario se genera */
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    /* se crea la empresa, el usuario creado es el dueño de la empresa */
    $company = Company::factory()->create([
        'owner_id' => $user->id,
    ]);
    $user->companies()->attach($company->id);

    seed(AreaSeeder::class);
    $area_x = Area::first();
    $area_y = Area::skip(1)->first();

    //* se crean dos registros para ejecutar el filtro
    $option_a = Project::factory()->create([
    'name' => 'Facturas fiscales',
    'start_date' => null,
    'end_date'   => null,
    'status'     => 'Pendiente',
    'by_user_id' => $user->id,
    'area_id'    => $area_y->id,
    'company_id' => $company->id,
    ]);

    $option_b = Project::factory()->create([
        'name' => 'Presentación del proyecto',
        'start_date' => now()->addDays(7)->toDateString(),
        'end_date'   => null,
        'status'     => 'En progreso',
        'by_user_id' => $user->id,
        'area_id'    => $area_x->id,
        'company_id' => $company->id,
    ]);

    // ! se introduce un valor al filtro que no coincide con los registros
    $filter = Project::filter([
        'status' => 'Completado',
    ])->get();

    //* se espera que no retorne nada al no tener coincidencias
    expect($filter)->toBeEmpty();
});
