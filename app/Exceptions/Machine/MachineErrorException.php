<?php

namespace App\Exceptions\Machine;

use App\Services\ATM\MachineServiceInterface;
use Exception;

class MachineErrorException extends Exception
{
    protected $message = MachineServiceInterface::ATM_ERR;
}
