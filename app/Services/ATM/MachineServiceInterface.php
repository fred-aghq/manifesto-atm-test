<?php


namespace App\Services\ATM;


interface MachineServiceInterface
{
    function getTotalCashAvailable(): int;

    function withdrawCash(int $amount);

    function getAccountBalance(): int;

    function getOverdraftAvailability(): int;

    function validatePin(int $pin);

    function validateAccountNumber(int $accountNumber);

    function validateLogin(int $accountNumber, int $pin);

    function validateWithdrawal(int $amount);
}
