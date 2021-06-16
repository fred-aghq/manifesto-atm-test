<?php

namespace Tests\Unit;

use App\Console\Commands\ATM;
use App\Exceptions\Machine\MachineErrorException;
use App\Models\Customer;
use App\Models\Machine;
use App\Repositories\MachineRepository;
use App\Services\ATM\MachineService;
use App\Services\ATM\MachineServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Mockery\MockInterface;
use Tests\CreatesApplication;
use Tests\TestCase;

class MachineServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Machine::factory()->create();
    }

    /**
     * The ATM cannot dispense more money than it holds.
     * The customer cannot withdraw more funds then they have access to.
     * The ATM should not dispense funds if the pin is incorrect.
     * The ATM should not expose the customer balance if the pin is incorrect.
     * The ATM should only dispense the exact amounts requested.
     */

    public function test_it_does_not_dispense_more_money_than_it_holds()
    {
        $totalCash = 200;

        // Given that the machine has a low amount of money
        $machine = Machine::first();
        $machine->total_cash = $totalCash;
        $machine->save();

        $this->mock(
            MachineRepository::class,
            function(MockInterface $mock) use ($machine) {
                $mock->shouldReceive('getMachine')
                    ->andReturn($machine);
            });

        $unit = App::make(MachineServiceInterface::class);

        // When I try to withdraw more cash than is available, then a Machine Error Exception is thrown
        $this->expectException(MachineErrorException::class);
        $unit->withdrawCash(($machine->total_cash + 100));

        // And the machine's total cash does not change.
        $machine->refresh();
        $this->assertEquals($totalCash, $machine->total_cash);
    }

    public function test_it_does_not_dispense_funds_if_pin_is_incorrect()
    {
        $customer = Customer::factory()->create();
        $unit = App::make(MachineServiceInterface::class);
        $this->expectException();

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
