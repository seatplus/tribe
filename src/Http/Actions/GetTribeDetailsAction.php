<?php

namespace Seatplus\Tribe\Http\Actions;

use Closure;
use Illuminate\Support\Facades\Pipeline;
use Seatplus\Tribe\Contracts\Tribe;
use Seatplus\Tribe\TribeRepository;

class GetTribeDetailsAction
{
    public function __construct(
        private TribeRepository $tribe_repository
    ) {
    }

    public function execute(string $tribe_id): array
    {
        $tribe = $this->tribe_repository->getTribe($tribe_id);

        return Pipeline::send($tribe)
            ->through([
                function (Tribe $tribe, Closure $next) {
                    if ($tribe::isConnectorConfigured()) {
                        return $next($tribe);
                    }

                    return [
                        'status' => 'Missing Configuration',
                        'url' => $tribe::getConnectorConfigUrl(),
                    ];
                },
                function (Tribe $tribe, Closure $next) {
                    if ($tribe::isTribeSetup()) {
                        return $next($tribe);
                    }

                    return [
                        'status' => 'Incomplete Setup',
                        'url' => $tribe::getRegistrationUrl(),
                    ];
                },
                function (Tribe $tribe, Closure $next) {
                    if ($tribe::isTribeEnabled()) {
                        return $next($tribe);
                    }

                    return [
                        'status' => 'Disabled',
                        'can_enable' => auth()->user()->can('superuser'),
                    ];
                },
                function (Tribe $tribe, Closure $next) {

                    $user = $tribe::findUser(auth()->user()->getAuthIdentifier());

                    if ($user) {
                        return [
                            'status' => 'Registered',
                            'can_manage' => auth()->user()->can('superuser'),
                        ];
                    }

                    return [
                        'status' => 'Not Registered',
                        'url' => $tribe::getRegistrationUrl(),
                    ];
                },
            ])->thenReturn();

    }
}
