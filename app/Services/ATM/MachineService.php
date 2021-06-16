<?php

namespace App\Services\ATM;

use App\Exceptions\Customer\FundsErrorException;
use App\Exceptions\Customer\InvalidPinException;
use App\Exceptions\Customer\InvalidAccountNumberException;
use App\Exceptions\Machine\MachineErrorException;
use App\Models\Customer;
use App\Models\Machine;
use App\Models\Machine as MachineModel;
use App\Repositories\CustomerRepository;
use App\Repositories\MachineRepository;
use Orkhanahmadov\EloquentRepository\EloquentRepository;

class MachineService implements MachineServiceInterface
{
    public const ACCOUNT_ERR = 'ACCOUNT_ERR';
    public const FUNDS_ERR = 'FUNDS_ERR';
    public const ATM_ERR = 'ATM_ERR';
    public const WITHDRAWAL_SUCCESS = 'WITHDRAWAL_SUCCESS';

    private CustomerRepository $customerRepo;
    private MachineRepository $machineRepo;

    public function __construct(MachineRepository $machineRepository, CustomerRepository $customerRepository)
    {
        $this->machineRepo = $machineRepository;
        $this->customerRepo = $customerRepository;
    }

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

    }

    public function validateLogin(int $accountNumber, int $pin) {
        return (
            $this->validateAccountNumber($accountNumber)
            && $this->validatePin($pin)
        );
    }

    public function validateWithdrawal(int $amount)
    {
        if ($this->machineRepo->getMachine()->total_cash < $amount) {
            throw new MachineErrorException();
        }

        throw new FundsErrorException();
    }

    public function getTotalCashAvailable(): int
    {
        return 8000;
    }
}
