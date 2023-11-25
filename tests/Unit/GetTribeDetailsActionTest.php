<?php

const ADMIN_PERMISSION = 'superuser';
const VIEW_TRIBES_PERMISSION = 'view tribes';

use Seatplus\Tribe\Http\Actions\GetTribeDetailsAction;

use function Pest\Laravel\actingAs;

beforeEach(function () {

    $this->tribe_repository = app(\Seatplus\Tribe\TribeRepository::class);
});

it('returns correct status if tribe is not configured for admin or non-admin', function ($permission, $expected_status) {

    $tribe = getTribeMock();

    $tribe->shouldReceive('isConnectorConfigured')
        ->andReturn(false);

    $tribe->shouldReceive('getConnectorConfigUrl')
        ->andReturn('test_tribe');

    $id = addTribeToRepository($tribe);

    $this->givePermissionsToTestUser([$permission]);

    actingAs($this->test_user);

    $tribe_details = (new GetTribeDetailsAction($this->tribe_repository))->execute($id);

    expect($tribe_details)->toHaveKey('status');
    expect($tribe_details['status'])->toEqual($expected_status);
})->with([
    [ADMIN_PERMISSION, 'Missing Configuration'],
    [VIEW_TRIBES_PERMISSION, 'Disabled'],
]);

it('returns "Incomplete Setup" status if tribe is not setup for admins and "Disabled" for non-admins', function ($permission, $expected_status) {

    $tribe = getTribeMock();

    $tribe->shouldReceive('isConnectorConfigured')
        ->andReturn(true);

    $tribe->shouldReceive('isTribeSetup')
        ->andReturn(false);

    $tribe->shouldReceive('getRegistrationUrl')
        ->andReturn('test_tribe');

    $id = addTribeToRepository($tribe);

    $this->givePermissionsToTestUser([$permission]);

    actingAs($this->test_user);

    $tribe_details = (new GetTribeDetailsAction($this->tribe_repository))->execute($id);

    expect($tribe_details)->toHaveKey('status');
    expect($tribe_details['status'])->toEqual($expected_status);
})->with([
    [ADMIN_PERMISSION, 'Incomplete Setup'],
    [VIEW_TRIBES_PERMISSION, 'Disabled'],
]);

it('returns "Disabled" status if tribe is disabled', function ($permission, $can_enable) {

    $tribe = getTribeMock();

    $tribe->shouldReceive('isConnectorConfigured')
        ->andReturn(true);

    $tribe->shouldReceive('isTribeSetup')
        ->andReturn(true);

    $tribe->shouldReceive('isTribeEnabled')
        ->andReturn(false);

    $id = addTribeToRepository($tribe);

    $this->givePermissionsToTestUser([$permission]);

    actingAs($this->test_user);

    $tribe_details = (new GetTribeDetailsAction($this->tribe_repository))->execute($id);

    expect($tribe_details)->toHaveKey('status', 'Disabled');
    expect($tribe_details)->toHaveKey('can_enable', $can_enable);
})->with([
    [ADMIN_PERMISSION, true],
    [VIEW_TRIBES_PERMISSION, false],
]);

it('returns "Not Registered" status if user is not registered', function ($permission) {

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

    $this->givePermissionsToTestUser([$permission]);

    actingAs($this->test_user);

    $tribe_details = (new GetTribeDetailsAction($this->tribe_repository))->execute($id);

    expect($tribe_details)->toHaveKey('status', 'Not Registered');
})->with([ADMIN_PERMISSION, VIEW_TRIBES_PERMISSION]);

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
