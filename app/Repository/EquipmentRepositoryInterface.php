<?php

namespace App\Repository;

use App\Repository\RepositoryInterface;

interface EquipmentRepositoryInterface extends RepositoryInterface
{
    public function delete(int $id);
}