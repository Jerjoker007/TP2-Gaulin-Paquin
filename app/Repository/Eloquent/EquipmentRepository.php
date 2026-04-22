<?php

namespace App\Repository\Eloquent;

use App\Models\Equipment;
use App\Repository\EquipmentRepositoryInterface;
use App\Repository\Eloquent\BaseRepository;
use App\Exceptions\EquipmentInUseException;

class EquipmentRepository extends BaseRepository implements EquipmentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(Equipment::class);
    }

    public function delete(int $id)
    {
        $equipment = $this->model->findOrFail($id);

        if ($equipment->rentals()->exists()) {
            throw new EquipmentInUseException();
        }

        $equipment->delete($id);
    }
}