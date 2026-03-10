<?php
namespace App\Services\Contracts;

interface ResolucionServiceInterface extends ServiceInterface
{
    public function createCharge($id, $user): bool;
}
