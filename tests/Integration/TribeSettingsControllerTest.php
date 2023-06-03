<?php

use function Pest\Laravel\actingAs;

it('returns tribe settings', function () {

    $tribe = getTribeMock();

    $tribe->shouldReceive('isConnectorConfigured')
        ->andReturn(true);

    $tribe->shouldReceive('isTribeSetup')
        ->andReturn(true);

    $tribe->shouldReceive('isTribeEnabled')
        ->andReturn(true);

    $tribe->shouldReceive('getTribeSettings')
        ->andReturn([
            'test' => 'test',
        ]);

    $id = addTribeToRepository($tribe);

    $this->givePermissionsToTestUser(['view tribes']);

    $response = actingAs($this->test_user)
        ->get(route('tribe.settings', $id));

    $response->assertOk();

    $response->assertJson([
        'test' => 'test',
    ]);
});

it('updates tribe settings', function () {

    $tribe = getTribeMock();

    $tribe->shouldReceive('isConnectorConfigured')
        ->andReturn(true);

    $tribe->shouldReceive('isTribeSetup')
        ->andReturn(true);

    $tribe->shouldReceive('isTribeEnabled')
        ->andReturn(true);

    $tribe->shouldReceive('setTribeSettings')
        ->with([
            'suffix' => 'test',
        ]);

    $id = addTribeToRepository($tribe);

    $this->givePermissionsToTestUser(['view tribes']);

    $response = actingAs($this->test_user)
        ->put(route('tribe.settings', $id), [
            'suffix' => 'test',
        ]);

    // assert redirect
    $response->assertRedirect(route('tribe.index'));
});
