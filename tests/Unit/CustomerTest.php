<?php

namespace Tests\Unit;

use App\Console\Commands\ATM;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    /**
     *
     * The customer cannot withdraw more funds then they have access to.
     */
    public function test_it_cannot_withdraw_more_funds_than_it_has_access_to()
    {

    }

    public function test_it_can_withdraw_with_successful_login_and_valid_amount()
    {
        $accountNumber = 12345678;
        $pin = 1234;
        $cashAvailable = 8000;
        $customerBalance = 500;
        $customerOverdraftAvailibility = 100;

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
            ->expectsOutput($customerBalance . ' ' . $customerOverdraftAvailibility)
            ->expectsChoice(
                'Select an operation',
                'W',
                ['(B)alance enquiry', '(W)ithdraw cash']
            )
            ->expectsQuestion('Withdrawal amount', 100)
            ->assertExitCode(ATM::WITHDRAWAL_SUCCESS);
    }
}
