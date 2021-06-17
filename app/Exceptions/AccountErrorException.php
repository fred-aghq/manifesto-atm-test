<?php

namespace App\Exceptions;

use App\Services\ATM\MachineServiceInterface;
use Exception;

class AccountErrorException extends Exception
{
    protected $message = MachineServiceInterface::ACCOUNT_ERR;
}
