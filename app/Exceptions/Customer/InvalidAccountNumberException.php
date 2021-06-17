<?php

namespace App\Exceptions\Customer;

use App\Services\ATM\MachineServiceInterface;
use Exception;

class InvalidAccountNumberException extends Exception
{
    protected $message = MachineServiceInterface::ACCOUNT_ERR;
}
