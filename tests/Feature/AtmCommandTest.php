<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Machine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AtmCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        DB::table('machines')->truncate();
        DB::table('customers')->truncate();
        parent::tearDown();
    }

    public function test_it_requires_machine_initialisation_if_no_machine_set()
    {

    }

    public function test_it_responds_with_the_account_balance_after_withdrawal_operation()
    {

    }

    public function test_it_responds_with_the_account_balance()
    {

    }

    public function test_it_can_withdraw_with_successful_login_and_valid_amount()
    {
        $accountNumber = 12345678;
        $pin = 1234;
        $cashAvailable = 8000;
        $customerBalance = 500;
        $customerOverdraft = 100;
        $withdrawalAmount = 100;

        $machine = Machine::factory()->make();
        $machine->total_cash = $cashAvailable;
        $machine->save();

        $customer = new Customer();
        $customer->fill([
            'account_balance' => $customerBalance,
            'overdraft_available' => $customerOverdraft,
            'account_number' => $accountNumber,
            'pin' => $pin,
        ]);

        $customer->save();

        dump($customer->refresh());

        $this->artisan('manifesto:atm')
            ->expectsOutput($cashAvailable)
            ->expectsQuestion('Enter account number', $accountNumber)
            ->expectsQuestion('Enter PIN', $pin)
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

        $customer = Customer::first();

        // Customer balance now reflects new amount after withdrawal
        $this->assertEquals(($customerBalance - $withdrawalAmount), $customer->accountBalance);
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

        $customer = Customer::first();

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

        // Customer balance has not changed
        $this->assertEquals($customerBalance, $customer->accountBalance);
    }
}
