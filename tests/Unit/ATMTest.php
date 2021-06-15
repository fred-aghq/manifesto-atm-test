<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ATMTest extends TestCase
{
    /**
     * The ATM cannot dispense more money than it holds.
     * The customer cannot withdraw more funds then they have access to.
     * The ATM should not dispense funds if the pin is incorrect.
     * The ATM should not expose the customer balance if the pin is incorrect.
     * The ATM should only dispense the exact amounts requested.
     */

    public function test_it_does_not_dispense_more_money_than_it_holds()
    {
        $this->assertTrue(false, "test not written");
    }

    public function test_it_does_not_dispense_funds_if_pin_is_incorrect()
    {
        $this->assertTrue(false, "test not written");
    }

    public function test_it_does_not_expose_customer_balance_if_pin_is_incorrect()
    {
        $this->assertTrue(false, "test not written");
    }
}
