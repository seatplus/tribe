<?php

use Illuminate\Support\Facades\Route;
use Seatplus\Auth\Http\Middleware\CheckPermissionOrCorporationRole;

Route::middleware(['web', 'auth', CheckPermissionOrCorporationRole::class.':view tribes'])
    ->prefix('tribe')
    ->group(function () {
        Route::controller(\Seatplus\Tribe\Http\Controllers\TribeController::class)
            ->group(function () {
                Route::get('/', 'index')->name('tribe.index');
                Route::get('/{tribe_id}', 'show')->name('tribe.show');
                Route::post('/{tribe_id}', 'update')->name('tribe.update');
                Route::delete('/{tribe_id}', 'destroy')->name('tribe.destroy');
            });

        Route::controller(\Seatplus\Tribe\Http\Controllers\TribeSettingsController::class)
            ->group(function () {
                Route::get('/{tribe_id}/settings', 'show')->name('tribe.settings');
                Route::put('/{tribe_id}/settings', 'update')->name('tribe.settings.update');
            });
    });
