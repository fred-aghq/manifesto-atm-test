<?php

namespace Tests\Unit;

use App\Exceptions\Customer\InvalidAccountNumberException;
use App\Exceptions\Customer\InvalidPinException;
use App\Exceptions\Machine\MachineErrorException;
use App\Models\Customer;
use App\Models\Machine;
use App\Repositories\CustomerRepository;
use App\Repositories\MachineRepository;
use App\Services\ATM\MachineServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Mockery\MockInterface;
use Tests\TestCase;

class MachineServiceTest extends TestCase
{
    use RefreshDatabase;

    private $unit;

    protected function setUp(): void
    {
        parent::setUp();
        Machine::factory()->create();
    }

    /*
     * The ATM should only dispense the exact amounts requested.
     */

    public function test_it_only_dispenses_the_exact_amounts_required()
    {
        $machine = Machine::first();
        $customer = Customer::factory()->create();

        $totalCash = $machine->total_cash;
        $withdrawalAmount = 100;

        $this->mock(
            MachineRepository::class,
            function (MockInterface $mock) use ($machine) {
                $mock->shouldReceive('getMachine')
                    ->andReturn($machine);
            });

        $this->mock(
            CustomerRepository::class,
            function (MockInterface $mock) use ($customer) {
                $mock->shouldReceive('findByAccountNumber')
                    ->andReturn($customer);
            }
        );

        $unit = App::make(MachineServiceInterface::class);

        $unit->login($customer->account_number, $customer->pin);
        $this->assertTrue($unit->withdrawCash($withdrawalAmount));
        $this->assertEquals($totalCash - $withdrawalAmount, $machine->refresh()->total_cash);
    }

    public function test_it_does_not_dispense_more_money_than_it_holds()
    {
        $totalCash = 200;

        // Given that the machine has a low amount of money
        $machine = Machine::factory()->create([
            'total_cash' => $totalCash,
        ]);

        $customer = Customer::factory()->create([
            'account_balance' => $machine->total_cash + 100 // sufficient funds
        ]);

        $this->mock(
            MachineRepository::class,
            function (MockInterface $mock) use ($machine) {
                $mock->shouldReceive('getMachine')
                    ->andReturn($machine);
            });

        $this->mock(
            CustomerRepository::class,
            function (MockInterface $mock) use ($customer) {
                $mock->shouldReceive('findByAccountNumber')
                    ->andReturn($customer);
            }
        );

        // When I try to withdraw more cash than is available, then a Machine Error Exception is thrown
        $this->expectException(MachineErrorException::class);

        $unit = App::make(MachineServiceInterface::class);



        $unit->login($customer->account_number, $customer->pin);
        $unit->withdrawCash(($machine->total_cash + 100));

        // And the machine's total cash does not change.
        $machine->refresh();
        $this->assertEquals($totalCash, $machine->total_cash);
    }

    public function test_it_does_not_continue_if_account_number_not_valid()
    {
        $this->expectException(InvalidAccountNumberException::class);
    }

    public function test_it_does_not_dispense_funds_if_pin_is_incorrect()
    {
        $this->expectException(InvalidPinException::class);
    }

    public function test_it_does_not_expose_customer_balance_if_pin_is_incorrect()
    {
        $this->assertTrue(false, "test not written");
    }
}
