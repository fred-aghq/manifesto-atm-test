<?php

namespace App\Services\ATM;


class MachineService implements MachineServiceInterface
{
    public function withdrawCash(){}
    public function getAccountBalance(): int {
        return 500 . ' ' . 100;
    }
    public function getOverdraftAvailability(): int {}
    public function validatePin(int $pin) {}
    public function validateAccountNumber(int $accountNumber) {}

    public function validateLogin(int $accountNumber, int $pin) {
        $this->validateAccountNumber($accountNumber);
        $this->validatePin($pin);
    }

    public function getTotalCashAvailable(): int
    {
        return 8000;
    }
}
