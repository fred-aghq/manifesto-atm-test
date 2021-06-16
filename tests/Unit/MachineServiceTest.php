<?php

namespace Tests\Unit;

use App\Exceptions\AccountErrorException;
use App\Exceptions\Customer\FundsErrorException;
use App\Exceptions\Customer\InvalidAccountNumberException;
use App\Exceptions\Machine\MachineErrorException;
use App\Models\Customer;
use App\Models\Machine;
use App\Repositories\CustomerRepository;
use App\Repositories\MachineRepository;
use App\Services\ATM\MachineServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery\MockInterface;
use Tests\TestCase;

class MachineServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        DB::table('machines')->truncate();
        DB::table('customers')->truncate();
        parent::tearDown();
    }

    /*
     * The ATM should only dispense the exact amounts requested.
     */

    public function test_it_only_dispenses_the_exact_amounts_required()
    {
        $machine = Machine::factory()->create();
        $customer = Customer::factory()->make([
            'account_number' => 12345678,
            'pin' => 1234,
        ]);

        $customer->save();

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

        $unit = $this->app->make(MachineServiceInterface::class);

        // Given that I log in with a valid account and PIN
        $unit->login(12345678, 1234);

        // When I withdraw cash
        $withdrawnAmount = $unit->withdrawCash($withdrawalAmount);

        // The exact amount required is dispensed
        $this->assertEquals($withdrawalAmount, $withdrawnAmount);

        // And the ATM correctly adjusts its total cash
        $this->assertEquals($totalCash - $withdrawalAmount, $machine->refresh()->total_cash);
    }

    public function test_it_does_not_dispense_more_money_than_it_holds()
    {
        $totalCash = 200;

        // Given that the machine has a low amount of money
        $machine = Machine::factory()->make([
            'total_cash' => $totalCash,
        ]);

        $machine->save();

        $customer = Customer::factory()->make([
            'account_number' => 12345678,
            'pin' => 1234,
            'account_balance' => ($machine->total_cash + 100) // sufficient funds but more than the ATM can provide
        ]);

        $customer->save();

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

        $unit = $this->app->make(MachineServiceInterface::class);

        // When I try to withdraw more cash than is available, then a Machine Error Exception is thrown
        $this->expectException(MachineErrorException::class);

        $unit->login($customer->account_number, $customer->pin);
        $unit->withdrawCash(($machine->total_cash + 100));

        // And the machine's total cash does not change.
        $machine->refresh();
        $this->assertEquals($totalCash, $machine->total_cash);
    }

    public function test_it_does_not_continue_if_account_number_not_valid()
    {
        $this->expectException(InvalidAccountNumberException::class);
        Machine::factory()->create();
        $customer = Customer::factory()->make([
            'account_number' => 99999999,
            'pin' => 4444,
        ]);
        $customer->save();

        $this->mock(MachineRepository::class);
        $this->mock(CustomerRepository::class);

        $unit = $this->app->make(MachineServiceInterface::class);
        $unit->login(11111111, 5555);
    }

    public function test_it_does_not_dispense_funds_if_pin_is_incorrect()
    {
        $this->expectException(InvalidAccountNumberException::class);
        $machine = Machine::factory()->create();
        $initialTotalCash = $machine->total_cash;

        $customer = Customer::factory()->make([
            'account_number' => 99999999,
            'pin' => 1111,
        ]);
        $customer->save();

        $this->mock(
            MachineRepository::class,
            function (MockInterface $mock) use ($machine) {
                $mock->shouldReceive('getMachine')
                    ->andReturn($machine);
            });

        $this->mock(CustomerRepository::class);

        $unit = $this->app->make(MachineServiceInterface::class);
        $unit->login(11111111, 9999);
        $this->assertEquals($initialTotalCash, $this->machine->refresh()->total_cash);

        $this->expectException(AccountErrorException::class);
        $unit->withdraw(1000);
    }

    public function test_it_allows_going_overdrawn_when_customer_has_one()
    {
        $machine = Machine::factory()->create();
        $customer = Customer::factory()->make([
            'account_number' => 12345678,
            'pin' => 1234,
            'overdraft_available' => 100,
            'account_balance' => 100,
        ]);

        $customer->save();

        $withdrawalAmount = 200;

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

        $unit = $this->app->make(MachineServiceInterface::class);

        // Given that I log in with a valid account and PIN
        $unit->login(12345678, 1234);

        // When I withdraw more cash than i have in my balance, but within my overdraft
        $withdrawnAmount = $unit->withdrawCash($withdrawalAmount);

        // The exact amount required is dispensed
        $this->assertEquals($withdrawalAmount, $withdrawnAmount);

        // And my balance is the correct negative amount
        $this->assertEquals(-100, $customer->refresh()->account_balance);
    }

    public function test_it_does_not_dispense_funds_if_it_exceeds_overdraft()
    {
        $machine = Machine::factory()->create();
        $customer = Customer::factory()->make([
            'account_number' => 12345678,
            'pin' => 1234,
            'overdraft_available' => 100,
            'account_balance' => 0,
        ]);

        $customer->save();

        $withdrawalAmount = 200;

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

        $unit = $this->app->make(MachineServiceInterface::class);

        // Given that I log in with a valid account and PIN
        $unit->login(12345678, 1234);

        $this->expectException(FundsErrorException::class);

        // When I withdraw more than my overdraft allows
        $withdrawnAmount = $unit->withdrawCash($withdrawalAmount);

        // The expected exception is thrown
    }

    public function test_it_does_not_dispense_funds_if_it_has_no_overdraft()
    {
        $machine = Machine::factory()->create();
        $customer = Customer::factory()->make([
            'account_number' => 12345678,
            'pin' => 1234,
            'overdraft_available' => 0,
            'account_balance' => 0,
        ]);

        $customer->save();

        $withdrawalAmount = 200;

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

        $unit = $this->app->make(MachineServiceInterface::class);

        // Given that I log in with a valid account and PIN
        $unit->login(12345678, 1234);

        $this->expectException(FundsErrorException::class);

        // When I attempt to withdraw with no available funds
        $unit->withdrawCash($withdrawalAmount);

        // The expected exception is thrown
    }
}
