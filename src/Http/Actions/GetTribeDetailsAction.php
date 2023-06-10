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

        $pipes = match (auth()->user()->can('superuser')) {
            true => $this->getAdminArray(),
            default => $this->getNonAdminArray(),
        };

        return Pipeline::send($tribe)
            ->through([
                ...$pipes,
                function (Tribe $tribe) {

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

    private function getAdminArray(): array
    {
        return [
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
                    'can_enable' => true,
                ];
            },
        ];
    }

    private function getNonAdminArray(): array
    {
        return [
            function (Tribe $tribe, Closure $next) {
                if ($tribe::isConnectorConfigured() && $tribe::isTribeSetup() && $tribe::isTribeEnabled()) {
                    return $next($tribe);
                }

                return [
                    'status' => 'Disabled',
                    'can_enable' => false,
                ];
            },
        ];
    }
}
