<?php

use Illuminate\Testing\Fluent\AssertableJson;
use Inertia\Testing\AssertableInertia;

it('denies access to the tribe index page', function () {

    $route = route('tribe.index');

    test()->actingAs($this->test_user)->get($route)
        ->assertStatus(401);
});

it('shows the tribe index page', function () {

    $route = route('tribe.index');

    $this->givePermissionsToTestUser(['view tribes']);

    test()->actingAs($this->test_user)->get($route)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Tribe/TribeIndex')
            ->has('tribes')
        );
});

it('shows the tribe details page', function () {

    $tribe = getControllerTribeMock();

    $tribe_id = addTribeToRepository($tribe);

    $route = route('tribe.show', ['tribe_id' => $tribe_id]);

    $this->givePermissionsToTestUser(['view tribes']);

    // assert that status is missing configuration
    test()->actingAs($this->test_user)
        ->get($route)
        ->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('status')
            ->where('status', 'Missing Configuration')
            ->etc()
        );
});

it('redirects user after update of tribe', function () {

    $tribe = getControllerTribeMock();

    $tribe->shouldReceive('enableTribe')
        ->once();

    $tribe_id = addTribeToRepository($tribe);

    $route = route('tribe.update', ['tribe_id' => $tribe_id]);

    $this->givePermissionsToTestUser(['view tribes']);

    test()->actingAs($this->test_user)
        ->post($route)
        ->assertRedirect(route('tribe.index'));
});

it('redirects user after disable of tribe', function () {

    $this->mock(\Seatplus\Tribe\TribeRepository::class, function ($mock) {

        $tribeMock = Mockery::mock(\Seatplus\Tribe\Contracts\Tribe::class);

        $tribeMock->shouldReceive('disableTribe')
            ->once();

        $mock->shouldReceive('getTribe')
            ->once()
            ->andReturn($tribeMock);
    });

    $route = route('tribe.destroy', ['tribe_id' => '1234']);

    $this->givePermissionsToTestUser(['view tribes']);

    test()->actingAs($this->test_user)
        ->delete($route)
        ->assertRedirect(route('tribe.index'));
});

it('seeds schedules tables with commands', function () {

    $schedules = \Seatplus\Eveapi\Models\Schedules::all();

    // assert that ApplyNicknamePolicyCommand is scheduled
    expect($schedules->contains('job', \Seatplus\Tribe\Commands\ApplyNicknamePolicyCommand::class))->toBeTrue()
        // assert that RunSquadSyncCommand is scheduled
        ->and($schedules->contains('job', \Seatplus\Tribe\Commands\RunSquadSyncCommand::class))->toBeTrue();

});

function getControllerTribeMock(): Mockery\MockInterface
{
    $tribe = getTribeMock();

    $tribe->shouldReceive('isConnectorConfigured')
        ->andReturn(false);

    $tribe->shouldReceive('getConnectorConfigUrl')
        ->andReturn('test_tribe');

    return $tribe;
}
