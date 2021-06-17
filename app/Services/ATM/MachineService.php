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
use Cassandra\Custom;
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

    public function logout(): void
    {
        $this->customer = null;
    }

    public function loggedIn(): bool
    {
        return !is_null($this->customer);
    }

    public function getTotalCashAvailable(): int
    {
        return $this->getMachine()->total_cash;
    }

    public function withdrawCash(int $amount): int
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
            return $amount;
        }

        return 0;
    }

    public function getCustomerBalance(): int
    {
        return $this->customer->account_balance;
    }

    public function getOverdraftAvailability(): int
    {
        return $this->customer->overdraft_available;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
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
                'exists:customers,account_number',
                'required',
                'numeric',
                'digits:8',
            ],
        ]);

        return !$accountNumberValidator->fails();
    }

    private function validatePin(Customer $customer, int $pin)
    {
        $pinValidator = Validator::make(['pin' => $pin], [
            'pin' => [
                'required',
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
