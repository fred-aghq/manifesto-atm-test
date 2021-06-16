<?php

namespace App\Services\ATM;

use App\Exceptions\Customer\FundsErrorException;
use App\Exceptions\Customer\InvalidAccountNumberException;
use App\Exceptions\Customer\InvalidPinException;
use App\Exceptions\Machine\MachineErrorException;
use App\Models\Customer;
use App\Repositories\CustomerRepository;
use App\Repositories\MachineRepository;
use App\Rules\ValidPin;
use Illuminate\Support\Facades\Validator;

class MachineService implements MachineServiceInterface
{


    private CustomerRepository $customerRepo;
    private MachineRepository $machineRepo;
    private Customer $customer;

    public function __construct(MachineRepository $machineRepository, CustomerRepository $customerRepository)
    {
        $this->machineRepo = $machineRepository;
        $this->customerRepo = $customerRepository;
    }

    public function withdrawCash(int $amount)
    {

    }

    private function validateAccountNumber(int $accountNumber)
    {
        $accountNumberValidator = Validator::make(['account_number' => $accountNumber], [
            'account_number' => [
                'required',
                'bail',
                'size:8',
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
                'size:4',
                new ValidPin($customer),
            ],
        ]);

        return !$pinValidator->fails();
    }

    public function login(int $accountNumber, int $pin): bool
    {
        if (!$this->validateAccountNumber($accountNumber)) {
            throw new InvalidAccountNumberException();
        }

        $customer = $this->customerRepo->findByAccountNumber($accountNumber);

        if ($this->validatePin($customer, $pin)) {
            $this->customer = $customer;
            return true;
        }

        return false;
    }

    private function validateWithdrawal(int $amount): bool
    {
        if (!$this->customer) {
            throw new MachineErrorException();
        }

        if ($this->getTotalCashAvailable() < $amount) {
            throw new MachineErrorException();
        }

        if ($this->customer->totalFundsAvailable < $amount) {
            throw new FundsErrorException();
        }
    }

    public function getTotalCashAvailable(): int
    {
        return $this->machineRepo->getMachine()->total_cash;
    }
}
