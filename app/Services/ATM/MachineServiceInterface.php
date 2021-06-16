<?php


namespace App\Services\ATM;


interface MachineServiceInterface
{
    public function getTotalCashAvailable(): int;
    public function withdrawCash();
    public function getAccountBalance(): int;
    public function getOverdraftAvailability(): int;
    public function validatePin(int $pin);
    public function validateAccountNumber(int $accountNumber);
    public function validateLogin(int $accountNumber, int $pin);
}
