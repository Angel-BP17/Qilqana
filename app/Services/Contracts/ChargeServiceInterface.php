<?php
namespace App\Services\Contracts;

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

interface ChargeServiceInterface extends ServiceInterface
{
    public function signStore(array $data, array $files, int $chargeId, int $userId): bool;

    public function reject(array $data, int $chargeId, int $userId): bool;

    public function getReportSentData(array $criteria, User $user, $defaultPeriod): Response;

    public function getReportCreatedData(array $criteria, User $user, $defaultPeriod): Response;

    public function getReportResolutionData(array $criteria, User $user, $defaultPeriod): Response;

    public function getReportReceivedData(array $criteria, User $user, $defaultPeriod): Response;
}
