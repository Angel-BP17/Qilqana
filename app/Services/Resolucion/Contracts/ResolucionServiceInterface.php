<?php

namespace App\Services\Resolucion\Contracts;

use App\Services\Base\Contracts\ServiceInterface;

interface ResolucionServiceInterface extends ServiceInterface
{
    public function generateChargeForResolucion($id, $user): bool;
}
