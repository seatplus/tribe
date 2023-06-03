<?php

use function Pest\Laravel\artisan;

it('can run tribe:nickname commands', function () {

    // create new console that will be called
    $command = new \Seatplus\Tribe\Tests\Stubs\DummyCommand();

    $tribe = getTribeMock();

    $tribe->shouldReceive('getNicknamePolicyCommandImplementation')
        ->once()
        ->andReturn(\Seatplus\Tribe\Tests\Stubs\DummyCommand::class);

    addTribeToRepository($tribe);

    artisan('tribe:nickname')
        ->expectsOutput('All done');
});

it('can run tribe:squads commands', function () {

    // create new console that will be called
    $command = new \Seatplus\Tribe\Tests\Stubs\DummyCommand();

    $tribe = getTribeMock();

    $tribe->shouldReceive('getSquadSyncCommandImplementation')
        ->once()
        ->andReturn(\Seatplus\Tribe\Tests\Stubs\DummyCommand::class);

    addTribeToRepository($tribe);

    artisan('tribe:squads')
        ->expectsOutput('All done');
});

it('can run tribe:all commands', function () {

    // create new console that will be called
    $command = new \Seatplus\Tribe\Tests\Stubs\DummyCommand();

    $tribe = getTribeMock();

    $tribe->shouldReceive('getNicknamePolicyCommandImplementation')
        ->once()
        ->andReturn(\Seatplus\Tribe\Tests\Stubs\DummyCommand::class);

    $tribe->shouldReceive('getSquadSyncCommandImplementation')
        ->once()
        ->andReturn(\Seatplus\Tribe\Tests\Stubs\DummyCommand::class);

    addTribeToRepository($tribe);

    artisan('tribe:all')
        ->assertSuccessful();
});
