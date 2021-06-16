<?php

namespace App\Console\Commands;

use App\Exceptions\Customer\InvalidAccountNumberException;
use App\Exceptions\Customer\InvalidPinException;
use App\Services\ATM\MachineService;
use App\Services\ATM\MachineServiceInterface;
use Illuminate\Console\Command;

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
        do {
            $this->line($this->service->getTotalCashAvailable());
            $this->line('');

            if (!$this->service->loggedIn())
            {
                $inputAccountNumber = $this->ask('Enter account number');

                if (empty($inputAccountNumber)) {
                    return 0;
                }

                $inputPin = $this->secret('Enter PIN');

                $this->service->login($inputAccountNumber, $inputPin);
            }

            $this->line($this->balanceEnquiry());

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
                    $this->balanceEnquiry();
                    break;
                case 'W':
                    $this->withdrawCash();
                    $this->line($this->balanceEnquiry());
                    break;
                case 'D':
                    $this->service->logout();
                    break;
                case 'E':
                    return 0;
            }
        } while (true);
    }

    private function balanceEnquiry()
    {
        return $this->service->getCustomerBalance() . ' ' . $this->service->getOverdraftAvailability();
    }

    private function withdrawCash()
    {
        $amount = $this->ask('Withdrawal amount');
        $this->service->withdrawCash($amount);
    }
}
