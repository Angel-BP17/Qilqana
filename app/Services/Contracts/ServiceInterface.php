<?php

namespace App\Services\Contracts;

interface ServiceInterface
{
    public function getAll(array $data): array;

    public function create(array $data): bool;

    public function update(array $data, int $id): bool;

    public function delete(array $data, int $id): bool;
}
