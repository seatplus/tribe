<?php

namespace Seatplus\Tribe\Tests\Stubs;

class ConsoleKernel extends \Orchestra\Testbench\Foundation\Console\Kernel
{
    protected $commands = [
        DummyCommand::class,
    ];
}
