<?php

beforeEach(function () {

    $this->tribe_repository = app(\Seatplus\Tribe\TribeRepository::class);

    $tribe = \Mockery::mock(\Seatplus\Tribe\Contracts\Tribe::class);

    // set mock for getName
    $tribe->shouldReceive('getName')
        ->andReturn('test_tribe');

    // set mock for getImg
    $tribe->shouldReceive('getImg')
        ->andReturn('test_tribe');

    $this->tribe = $tribe;
});

it('throws error if get tribe is queried on empty TribeRepository', function () {

    $this->tribe_repository->getTribe('unknown_tribe_id');

})->throws(\Exception::class);

it('throws error if unknown tribe is queried with a non empty TribeRepository', function () {
    $this->tribe_repository->add($this->tribe);

    $this->tribe_repository->getTribe('unknown_tribe_id');

})->throws(\Exception::class);

it('returns tribe via tribe_repository', function () {

    $this->tribe_repository->add($this->tribe);

    $id = base64_encode(get_class($this->tribe));

    $tribe = $this->tribe_repository->getTribe($id);

    expect($tribe)->toBeInstanceOf(\Seatplus\Tribe\Contracts\Tribe::class);
});

it('return all tribes', function () {

    $this->tribe_repository->add($this->tribe);

    $tribes = $this->tribe_repository->getTribes();

    expect($tribes)->toHaveCount(1);
});
