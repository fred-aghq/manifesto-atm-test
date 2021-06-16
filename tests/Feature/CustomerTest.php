<?php

namespace Tests\Feature;

use App\Console\Commands\ATM;
use App\Models\Customer;
use App\Services\ATM\MachineService;
use Cassandra\Custom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_withdraw_with_successful_login_and_valid_amount()
    {
        $accountNumber = 12345678;
        $pin = 1234;
        $cashAvailable = 8000;
        $customerBalance = 500;
        $customerOverdraft = 100;
        $withdrawalAmount = 100;

        $customer = Customer::factory([
            'account_number' => $accountNumber,
            'pin' => $pin,
            'account_balance' => $customerBalance,
            'overdraft_available' => $customerOverdraft,
        ])
            ->create();

        $this->artisan('manifesto:atm')
            ->expectsOutput($cashAvailable)
            ->expectsQuestion('Enter account number', '12345678')
            ->expectsQuestion('Enter PIN', '1234')
            ->expectsOutput($accountNumber . ' ' . $pin . ' ' . $pin)
            ->expectsChoice(
                'Select an operation',
                'B',
                ['(B)alance enquiry', '(W)ithdraw cash']
            )
            ->expectsOutput($customerBalance . ' ' . $customerOverdraft)
            ->expectsChoice(
                'Select an operation',
                'W',
                ['(B)alance enquiry', '(W)ithdraw cash']
            )
            ->expectsQuestion('Withdrawal amount', $withdrawalAmount)
            ->assertExitCode(0);

        $this->assertEquals(($customerBalance - $withdrawalAmount), $customer->refresh()->accountBalance);
    }

    /**
     *
     * The customer cannot withdraw more funds then they have access to.
     */
    public function test_it_cannot_withdraw_more_funds_than_it_has_access_to()
    {
        $accountNumber = 12345678;
        $pin = 1234;
        $cashAvailable = 8000;
        $customerBalance = 500;
        $customerOverdraft = 100;
        $withdrawalAmount = 700;

        $customer = Customer::factory([
            'account_number' => $accountNumber,
            'pin' => $pin,
            'account_balance' => $customerBalance,
            'overdraft_available' => $customerOverdraft,
        ])
            ->create();

        $this->artisan('manifesto:atm')
            ->expectsOutput($cashAvailable)
            ->expectsQuestion('Enter account number', '12345678')
            ->expectsQuestion('Enter PIN', '1234')
            ->expectsOutput($accountNumber . ' ' . $pin . ' ' . $pin)
            ->expectsChoice(
                'Select an operation',
                'B',
                ['(B)alance enquiry', '(W)ithdraw cash']
            )
            ->expectsOutput($customerBalance . ' ' . $customerOverdraft)
            ->expectsChoice(
                'Select an operation',
                'W',
                ['(B)alance enquiry', '(W)ithdraw cash']
            )
            ->expectsQuestion('Withdrawal amount', $withdrawalAmount)
            ->assertExitCode('FUNDS_ERR');

        $this->assertEquals(($customerBalance - $withdrawalAmount), $customer->refresh()->accountBalance);
    }
}
