<?php

namespace Seatplus\Tribe\Commands;

use Illuminate\Console\Command;
use Seatplus\Tribe\TribeRepository;

class RunSquadSyncCommand extends Command
{
    public $signature = 'tribe:squads';

    public $description = 'Sync squads';

    public function handle(TribeRepository $repository)
    {
        $tribes = $repository->getTribes();

        foreach ($tribes as $tribe) {
            $tribe = $repository->getTribe($tribe['id']);

            $this->comment('Running squad sync command for '.$tribe::getName());

            $tribe_command = $tribe->getSquadSyncCommandImplementation();

            $this->call($tribe_command);
        }

        $this->comment('All done');
    }
}
