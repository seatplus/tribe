<?php

namespace Seatplus\Tribe\Commands;

use Illuminate\Console\Command;

class RunTribeCommands extends Command
{
    public $signature = 'tribe:all';

    public $description = 'Run all tribe commands';

    public function handle()
    {
        $this->call(ApplyNicknamePolicyCommand::class);
        $this->call(RunSquadSyncCommand::class);

        $this->comment('All done');
    }
}
