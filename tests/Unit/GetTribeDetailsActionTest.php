<?php

use function Pest\Laravel\actingAs;
use Seatplus\Tribe\Http\Actions\GetTribeDetailsAction;

beforeEach(function () {

    $this->tribe_repository = app(\Seatplus\Tribe\TribeRepository::class);
});

it('returns "Missing Configuration" status if tribe is not configured', function () {

    $tribe = getTribeMock();

    $tribe->shouldReceive('isConnectorConfigured')
        ->andReturn(false);

    $tribe->shouldReceive('getConnectorConfigUrl')
        ->andReturn('test_tribe');

    $id = addTribeToRepository($tribe);

    $tribe_details = (new GetTribeDetailsAction($this->tribe_repository))->execute($id);

    expect($tribe_details)->toHaveKey('status');
    expect($tribe_details['status'])->toEqual('Missing Configuration');
});

it('returns "Incomplete Setup" status if tribe is not setup', function () {

    $tribe = getTribeMock();

    $tribe->shouldReceive('isConnectorConfigured')
        ->andReturn(true);

    $tribe->shouldReceive('isTribeSetup')
        ->andReturn(false);

    $tribe->shouldReceive('getRegistrationUrl')
        ->andReturn('test_tribe');

    $id = addTribeToRepository($tribe);

    $tribe_details = (new GetTribeDetailsAction($this->tribe_repository))->execute($id);

    expect($tribe_details)->toHaveKey('status');
    expect($tribe_details['status'])->toEqual('Incomplete Setup');
});

it('returns "Disabled" status if tribe is disabled', function () {

    $tribe = getTribeMock();

    $tribe->shouldReceive('isConnectorConfigured')
        ->andReturn(true);

    $tribe->shouldReceive('isTribeSetup')
        ->andReturn(true);

    $tribe->shouldReceive('isTribeEnabled')
        ->andReturn(false);

    $id = addTribeToRepository($tribe);

    actingAs($this->test_user);

    $tribe_details = (new GetTribeDetailsAction($this->tribe_repository))->execute($id);

    expect($tribe_details)->toHaveKey('status');
    expect($tribe_details['status'])->toEqual('Disabled');
});

it('returns "Not Registered" status if user is not registered', function () {

    $tribe = getTribeMock();

    $tribe->shouldReceive('isConnectorConfigured')
        ->andReturn(true);

    $tribe->shouldReceive('isTribeSetup')
        ->andReturn(true);

    $tribe->shouldReceive('isTribeEnabled')
        ->andReturn(true);

    $tribe->shouldReceive('findUser')
        ->andReturn(null);

    $tribe->shouldReceive('getRegistrationUrl')
        ->andReturn('test_tribe');

    $id = addTribeToRepository($tribe);

    actingAs($this->test_user);

    $tribe_details = (new GetTribeDetailsAction($this->tribe_repository))->execute($id);

    expect($tribe_details)->toHaveKey('status');
    expect($tribe_details['status'])->toEqual('Not Registered');
});

it('returns "Registered" status if user is registered', function () {

    $tribe = getTribeMock();

    $tribe->shouldReceive('isConnectorConfigured')
        ->andReturn(true);

    $tribe->shouldReceive('isTribeSetup')
        ->andReturn(true);

    $tribe->shouldReceive('isTribeEnabled')
        ->andReturn(true);

    $user = Mockery::mock(Seatplus\Connector\Models\User::class);

    $tribe->shouldReceive('findUser')
        ->andReturn($user);

    $id = addTribeToRepository($tribe);

    actingAs($this->test_user);

    $tribe_details = (new GetTribeDetailsAction($this->tribe_repository))->execute($id);

    expect($tribe_details)->toHaveKey('status');
    expect($tribe_details['status'])->toEqual('Registered');
});
