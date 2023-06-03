<?php

namespace Seatplus\Tribe\Tests\Stubs;

class DummyCommand extends \Illuminate\Console\Command
{
    public $signature = 'test:dummy';

    public $description = 'this is a dummy command';

    public function handle()
    {
        $this->info('Dummy done');
    }
}
