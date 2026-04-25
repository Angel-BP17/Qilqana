<?php

namespace App\Services\Charge\Contracts;

use App\Services\Base\Contracts\ServiceInterface;

interface ChargeServiceInterface extends ServiceInterface
{
    public function signStore(array $data, array $files, int $chargeId, int $userId): bool;

    public function reject(array $data, int $chargeId, int $userId): bool;
}
