<?php

namespace App\Exceptions\Customer;

use App\Services\ATM\MachineServiceInterface;
use Exception;

class InvalidPinException extends Exception
{
    protected $message = MachineServiceInterface::ACCOUNT_ERR;
}
