<?php


namespace App\Services\ATM;


interface MachineServiceInterface
{
    public const ACCOUNT_ERR = 'ACCOUNT_ERR';
    public const FUNDS_ERR = 'FUNDS_ERR';
    public const ATM_ERR = 'ATM_ERR';
    public const WITHDRAWAL_SUCCESS = 'WITHDRAWAL_SUCCESS';

    function getTotalCashAvailable(): int;

    function withdrawCash(int $amount);

    function login(int $accountNumber, int $pin): bool;

    function getCustomerBalance(): int;
}
