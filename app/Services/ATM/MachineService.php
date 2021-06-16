<?php

namespace App\Services\ATM;

use App\Exceptions\AccountErrorException;
use App\Exceptions\Customer\FundsErrorException;
use App\Exceptions\Customer\InvalidAccountNumberException;
use App\Exceptions\Customer\InvalidPinException;
use App\Exceptions\Machine\MachineErrorException;
use App\Models\Customer;
use App\Models\Machine;
use App\Repositories\CustomerRepository;
use App\Repositories\MachineRepository;
use App\Rules\ValidPin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MachineService implements MachineServiceInterface
{


    private CustomerRepository $customerRepo;
    private MachineRepository $machineRepo;
    private ?Customer $customer = null;
    private ?Machine $machine = null;

    public function __construct(MachineRepository $machineRepository, CustomerRepository $customerRepository)
    {
        $this->machineRepo = $machineRepository;
        $this->customerRepo = $customerRepository;
    }

    public function login(int $accountNumber, int $pin): bool
    {
        if (!$this->validateAccountNumber($accountNumber)) {
            throw new InvalidAccountNumberException();
        }

        $customer = $this->customerRepo->findByAccountNumber($accountNumber);

        if (!$this->validatePin($customer, $pin)) {
            throw new InvalidPinException();
        }

        $this->customer = $customer;
        return true;
    }

    public function getTotalCashAvailable(): int
    {
        return $this->machineRepo->getMachine()->total_cash;
    }

    public function withdrawCash(int $amount): bool
    {
        if (!$this->customer) {
            throw new AccountErrorException();
        }

        $machine = $this->getMachine();

        if ($this->validateWithdrawal($amount)) {
            $machine->total_cash -= $amount;
            $this->customer->account_balance -= $amount;
            $machine->save();
            $this->customer->save();
            return true;
        }

        return false;
    }

    private function validateWithdrawal(int $amount): bool
    {
        if ($this->getTotalCashAvailable() < $amount) {
            throw new MachineErrorException();
        }

        if ($this->customer->totalFundsAvailable < $amount) {
            throw new FundsErrorException();
        }

        return true;
    }

    private function validateAccountNumber(int $accountNumber)
    {
        $accountNumberValidator = Validator::make(['account_number' => $accountNumber], [
            'account_number' => [
                'required',
                'bail',
                'numeric',
                'digits:8',
                'exists:customers,account_number'
            ],
        ]);

        return !$accountNumberValidator->fails();
    }

    private function validatePin(Customer $customer, int $pin)
    {
        $pinValidator = Validator::make(['pin' => $pin], [
            'pin' => [
                'required',
                'bail',
                'numeric',
                'digits:4',
                new ValidPin($customer),
            ],
        ]);

        return !$pinValidator->fails();
    }

    private function getMachine()
    {
        if (!$this->machine) {
            $this->machine = $this->machineRepo->getMachine();
        }

        return $this->machine;
    }
}
