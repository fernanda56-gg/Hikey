<?php

use function Pest\Laravel\{actingAs, get, seed};

use App\Models\Company;
use App\Models\User;

test('El usuario manager puede visualizar un proyecto en especifico', function () {
    /** @var \App\Models\User $manager */
    $manager = User::factory()->create();

    $company = Company::factory()->create([
        'owner_id' => $manager->id,
    ]);

    expect($manager->fresh()->hasRole('manager'))->toBeTrue();
});

