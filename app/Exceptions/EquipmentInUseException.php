<?php

namespace App\Exceptions;

use Exception;

class EquipmentInUseException extends Exception
{
    protected $message = 'Equipment is in used and cannot be deleted.';
}
