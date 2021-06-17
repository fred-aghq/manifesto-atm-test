<?php

namespace App\Exceptions\Customer;

use App\Services\ATM\MachineServiceInterface;
use Exception;

class FundsErrorException extends Exception
{
    protected $message = MachineServiceInterface::FUNDS_ERR;
}
