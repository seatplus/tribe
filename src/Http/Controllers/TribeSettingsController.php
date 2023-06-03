<?php

namespace Seatplus\Tribe\Http\Controllers;

use Illuminate\Http\Request;
use Seatplus\Tribe\TribeRepository;

class TribeSettingsController
{
    public function show(string $tribe_id, TribeRepository $repository)
    {

        $tribe = $repository->getTribe($tribe_id);

        return $tribe->getTribeSettings();

    }

    public function update(string $tribe_id, TribeRepository $repository, Request $request)
    {

        $request->validate([
            'prefix' => ['nullable', 'string'],
            'suffix' => ['nullable', 'string'],
            'ticker' => 'boolean',
        ]);

        $tribe = $repository->getTribe($tribe_id);

        $tribe->setTribeSettings($request->only(['prefix', 'suffix', 'ticker']));

        return redirect()->route('tribe.index');
    }
}
