<?php

use Illuminate\Database\Migrations\Migration;
use Seatplus\Eveapi\Models\Schedules;

return new class extends Migration
{
    public function up(): void
    {

        $jobs = [
            // Run ApplyNicknamePolicy Command every 15 minutes
            \Seatplus\Tribe\Commands\ApplyNicknamePolicyCommand::class => '*/15 * * * *',
            // Run RunSquadSyncCommand every 15 minutes
            \Seatplus\Tribe\Commands\RunSquadSyncCommand::class => '*/15 * * * *',
        ];
        // if the schedule is not in the database, create it
        foreach ($jobs as $job => $schedule) {
            Schedules::query()->firstOrCreate([
                'job' => $job,
            ], [
                'expression' => $schedule,
            ]);
        }
    }
};
