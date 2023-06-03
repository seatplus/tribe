<?php

namespace Seatplus\Tribe\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Seatplus\Auth\AuthenticationServiceProvider;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\EveapiServiceProvider;
use Seatplus\Tribe\Contracts\Tribe;
use Seatplus\Tribe\Tests\Stubs\ConsoleKernel;
use Seatplus\Tribe\Tests\Stubs\Kernel;
use Seatplus\Tribe\TribeServiceProvider;
use Seatplus\Web\Http\Middleware\Authenticate;
use Seatplus\Web\WebServiceProvider;
use Spatie\Permission\PermissionRegistrar;

class TestCase extends Orchestra
{
    use LazilyRefreshDatabase;

    public User $test_user;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => match (true) {
                Str::startsWith($modelName, 'Seatplus\Auth') => 'Seatplus\\Auth\\Database\\Factories\\'.class_basename($modelName).'Factory',
                Str::startsWith($modelName, 'Seatplus\Eveapi') => 'Seatplus\\Eveapi\\Database\\Factories\\'.class_basename($modelName).'Factory',
                Str::startsWith($modelName, 'Seatplus\Tribe') => 'Seatplus\\Tribe\\Database\\Factories\\'.class_basename($modelName).'Factory',
                default => dd('no match for '.$modelName)
            }
        );

        // Do not use the queue
        Queue::fake();

        $this->test_user = Event::fakeFor(fn () => User::factory()->create());

        Inertia::setRootView('web::app');
        $this->withoutVite();
    }

    protected function getPackageProviders($app)
    {
        return [
            TribeServiceProvider::class,
            EveapiServiceProvider::class,
            ServiceProvider::class,
            WebServiceProvider::class,
            AuthenticationServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config(['app.debug' => true]);

        $app['router']->aliasMiddleware('auth', Authenticate::class);

        // Use test User model for users provider
        $app['config']->set('auth.providers.users.model', User::class);

        $app['config']->set('cache.prefix', 'seatplus_tests---');

        //Setup Inertia for package development
        config()->set('inertia.testing.page_paths', array_merge(
            config()->get('inertia.testing.page_paths', []),
            [
                realpath(__DIR__.'/../resources/js/Pages'),
                //realpath(__DIR__ . '/../resources/js/Shared'),
            ],
        ));
    }

    /**
     * Resolve application HTTP Tribe implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationHttpKernel($app)
    {
        $app->singleton(\Illuminate\Contracts\Http\Kernel::class, Kernel::class);
    }

    /**
     * Resolve application Console Kernel implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton('Illuminate\Contracts\Console\Kernel', ConsoleKernel::class);
    }

    protected function givePermissionsToTestUser(array $array)
    {
        foreach ($array as $string) {
            $permission = Permission::findOrCreate($string);

            $this->test_user->givePermissionTo($permission);
        }

        // now re-register all the roles and permissions
        $this->app->make(PermissionRegistrar::class)->registerPermissions();
    }
}
