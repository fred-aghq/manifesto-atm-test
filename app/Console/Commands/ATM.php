<?php

namespace App\Console\Commands;

use App\Models\Machine;
use App\Services\ATM\MachineService;
use App\Services\ATM\MachineServiceInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * The CLI entrypoint for the ATM service.
 * @package App\Console\Commands
 */
class ATM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manifesto:atm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the ATM command-line application.';

    /**
     * The service that provides ATM functionality
     *
     * @var MachineService
     *
     */
    private MachineServiceInterface $service;

    /**
     * Create a new command instance.
     *
     * @param MachineServiceInterface $atmService
     */
    public function __construct(MachineServiceInterface $atmService)
    {
        parent::__construct();
        $this->service = $atmService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (Machine::all()->count() < 1) {
            $this->initialiseMachine();
        }
        do {
            try {
                if(!$this->checkLogin()) {
                    return 0;
                }
                $this->line($this->balanceEnquiry());
            } catch (\Exception $exception) {
                $this->printError($exception->getMessage());
                continue;
            }

            $action = $this->choice(
                'Select an operation',
                [
                    'B' => 'Balance enquiry',
                    'W' => 'Withdraw cash',
                    'D' => 'Different Account',
                    'E' => 'Exit',
                ]
            );

            switch ($action) {
                case 'B':
                    try {
                        $this->line($this->balanceEnquiry());
                    } catch (\Exception $exception) {
                        $this->printError($exception->getMessage());
                    }
                    break;
                case 'W':
                    try {
                        $this->withdrawCash();
                        $this->line($this->service->getCustomerBalance());
                    } catch (\Exception $exception) {
                        $this->printError($exception->getMessage());
                    }
                    break;
                case 'D':
                    $this->service->logout();
                    break;
                case 'E':
                    return 0;
            }
        } while (true);
    }

    private function balanceEnquiry(): string
    {
        return "{$this->service->getCustomerBalance()} {$this->service->getOverdraftAvailability()}";
    }

    private function accountDetails(): string
    {
        $customer = $this->service->getCustomer();
        return "{$customer->account_number} {$customer->pin}";
    }

    private function withdrawCash(): void
    {
        $amount = $this->ask('Withdrawal amount');
        $amountWithdrawn = $this->service->withdrawCash($amount);
        $this->line($amountWithdrawn);
    }

    private function initialiseMachine(): bool
    {
        $totalCash = $this->ask('Initialise ATM total cash');

        if (!is_numeric($totalCash)) {
            $this->error('Invalid value');
            return false;
        }

        $machine = new Machine();
        $machine->total_cash = floor($totalCash);
        $machine->save();
        return true;
    }

    private function checkLogin()
    {
        if (!$this->service->loggedIn()) {
            $inputAccountNumber = $this->ask('Enter account number');

            if (empty($inputAccountNumber)) {
                return 0;
            }

            $inputPin = $this->ask('Enter PIN');

            $this->service->login($inputAccountNumber, $inputPin);
            $this->line("{$this->accountDetails()} {$inputPin}");
            return true;
        }
    }

    private function printError(string $errorMessage)
    {
        $this->error($errorMessage);
    }

    private function exitWithError(\Exception $exception)
    {
        $this->printError($exception->getMessage());
        return 1;
    }
}
