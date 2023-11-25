<?php

namespace Seatplus\Tribe;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Seatplus\Tribe\Commands\ApplyNicknamePolicyCommand;
use Seatplus\Tribe\Commands\RunSquadSyncCommand;
use Seatplus\Tribe\Commands\RunTribeCommands;

class TribeServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        // Publish the JS & CSS,
        $this->addPublications();

        // Add routes
        $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');

        //Add Migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations/');

        // Add translations
        //$this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'web');

        // Add commands
        $this->addCommands();
    }

    public function register()
    {
        $this->mergeConfigurations();

        //$this->app->alias(Tribe::class, 'tribe');
        $this->app->singleton(TribeRepository::class, function ($app) {
            return new TribeRepository();
        });
    }

    private function mergeConfigurations()
    {

        $this->mergeConfigFrom(
            __DIR__.'/../config/sidebar.tribes.php', 'package.sidebar.connector'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../config/permissions.tribe.php',
            'web.permissions'
        );

        // add jobs to seatplus schedule
        $this->mergeConfigFrom(
            __DIR__.'/../config/jobs.php',
            'seatplus.updateJobs'
        );
    }

    private function addPublications()
    {
        /*
         * to publish assets one can run:
         * php artisan vendor:publish --tag=web --force
         * or use Laravel Mix to copy the folder to public repo of core.
         */
        $this->publishes([
            __DIR__.'/../resources/js' => resource_path('js'),
        ], 'web');
    }

    public function provides()
    {
        return [
            TribeRepository::class,
        ];
    }

    private function addCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RunTribeCommands::class,
                ApplyNicknamePolicyCommand::class,
                RunSquadSyncCommand::class,
            ]);
        }
    }
}
