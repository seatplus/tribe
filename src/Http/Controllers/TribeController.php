<?php

namespace Seatplus\Tribe\Http\Controllers;

use Seatplus\Tribe\Contracts\Tribe;
use Seatplus\Tribe\Http\Actions\GetTribeDetailsAction;
use Seatplus\Tribe\TribeRepository;

class TribeController
{
    private Tribe $tribe;

    public function index(TribeRepository $tribe)
    {

        return inertia('Tribe/TribeIndex', [
            'tribes' => array_values($tribe->getTribes()),
        ]);
    }

    public function show(string $tribe_id, GetTribeDetailsAction $get_tribe_details_action)
    {

        return $get_tribe_details_action->execute($tribe_id);
    }

    public function update(string $tribe_id, TribeRepository $repository)
    {
        $this->tribe = $repository->getTribe($tribe_id);

        $this->tribe::enableTribe();

        return redirect()->route('tribe.index');
    }

    public function destroy(string $tribe_id, TribeRepository $repository)
    {
        $this->tribe = $repository->getTribe($tribe_id);

        $this->tribe::disableTribe();

        return redirect()->route('tribe.index');
    }
}
