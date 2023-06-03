<?php

namespace Seatplus\Tribe\Commands;

use Illuminate\Console\Command;
use Seatplus\Tribe\TribeRepository;

class ApplyNicknamePolicyCommand extends Command
{
    public $signature = 'tribe:nickname';

    public $description = 'Applies nickname rules';

    public function handle(TribeRepository $repository)
    {
        $tribes = $repository->getTribes();

        foreach ($tribes as $tribe) {
            $tribe = $repository->getTribe($tribe['id']);

            $this->comment('Running nickname command for '.$tribe::getName());

            $tribe_command = $tribe->getNicknamePolicyCommandImplementation();

            $this->call($tribe_command);
        }

        $this->comment('All done');
    }
}
