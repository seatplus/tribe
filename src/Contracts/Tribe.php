<?php

namespace Seatplus\Tribe\Contracts;

use Seatplus\Connector\Contracts\Connector;

interface Tribe extends Connector
{
    public static function isTribeSetup(): bool;

    public static function isTribeEnabled(): bool;

    public static function disableTribe(): void;

    public static function enableTribe(): void;

    public static function getTribeSettings(): array;

    public static function setTribeSettings(array $settings): void;

    public static function getNicknamePolicyCommandImplementation(): string;

    public static function getSquadSyncCommandImplementation(): string;
}
