<?php

namespace Seatplus\Tribe;

use Seatplus\Tribe\Contracts\Tribe;

class TribeRepository
{
    private array $tribes = [];

    /**
     * @throws \Throwable
     */
    public function getTribe(string $tribe_id): Tribe
    {
        // throw error if tribe_id is not known
        throw_unless(array_key_exists($tribe_id, $this->tribes), \Exception::class, 'Tribe not found');

        return app($this->tribes[$tribe_id]['implementation']);
    }

    public function add(Tribe $tribe_implementation): void
    {

        $name = $tribe_implementation::getName();
        $img = $tribe_implementation::getImg();

        $tribe_implementation_class = get_class($tribe_implementation);
        $tribe_id = base64_encode($tribe_implementation_class);

        $this->tribes[$tribe_id] = [
            'name' => $name,
            'img' => $img,
            'id' => $tribe_id,
            'implementation' => $tribe_implementation_class,
        ];
    }

    public function getTribes(): array
    {
        return $this->tribes;
    }
}
