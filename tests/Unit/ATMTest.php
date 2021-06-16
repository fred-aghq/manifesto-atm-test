<?php

namespace Tests\Unit;

use App\Console\Commands\ATM;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesApplication;
use Tests\TestCase;

class ATMTest extends TestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    /**
     * The ATM cannot dispense more money than it holds.
     * The customer cannot withdraw more funds then they have access to.
     * The ATM should not dispense funds if the pin is incorrect.
     * The ATM should not expose the customer balance if the pin is incorrect.
     * The ATM should only dispense the exact amounts requested.
     */

    public function test_it_does_not_dispense_more_money_than_it_holds()
    {

    }

    public function test_it_does_not_dispense_funds_if_pin_is_incorrect()
    {
        $this->assertTrue(false, "test not written");
    }

    public function test_it_does_not_expose_customer_balance_if_pin_is_incorrect()
    {
        $this->assertTrue(false, "test not written");
    }

    public function test_it_displays_the_total_cash_held_on_the_first_line()
    {
        $this->assertTrue(false, "test not written");
    }
}
