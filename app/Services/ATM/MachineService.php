<?php

namespace App\Services\ATM;

use App\Exceptions\Customer\FundsErrorException;
use App\Exceptions\Customer\InvalidPinException;
use App\Exceptions\Machine\MachineErrorException;

class MachineService implements MachineServiceInterface
{
    public function withdrawCash(int $amount){
        $this->validateWithdrawal($amount);
    }

    public function getAccountBalance(): int {
        // @TODO: retrieve customer balance from db
        return 500;
    }
    public function getOverdraftAvailability(): int {
        // @TODO: retrieve customer overdraft from db
        return 100;
    }

    public function validatePin(int $pin) {
        throw new InvalidPinException();
    }
    public function validateAccountNumber(int $accountNumber) {
        throw new InvalidAccountNumberException();
    }

    public function validateLogin(int $accountNumber, int $pin) {
        return (
            $this->validateAccountNumber($accountNumber)
            && $this->validatePin($pin)
        );
    }

    public function validateWithdrawal(int $amount)
    {
        throw new FundsErrorException();
        throw new MachineErrorException();
    }

    public function getTotalCashAvailable(): int
    {
        return 8000;
    }
}
