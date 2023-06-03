<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use Seatplus\Tribe\Tests\TestCase;

/*uses(TestCase::class)
    ->group('integration')
    ->in('Integration');*/

uses(TestCase::class)
    ->in('Unit', 'Integration');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function getTribeMock(): Mockery\MockInterface
{
    $tribe = \Mockery::mock(\Seatplus\Tribe\Contracts\Tribe::class);

    // set mock for getName
    $tribe->shouldReceive('getName')
        ->andReturn('test_tribe');

    // set mock for getImg
    $tribe->shouldReceive('getImg')
        ->andReturn('test_tribe');

    return $tribe;
}

function addTribeToRepository(Mockery\MockInterface $tribe): string
{
    $tribe_repository = app(\Seatplus\Tribe\TribeRepository::class);

    $tribe_repository->add($tribe);

    return base64_encode(get_class($tribe));
}
