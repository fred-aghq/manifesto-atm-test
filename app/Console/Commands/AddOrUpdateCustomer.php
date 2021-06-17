<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class AddOrUpdateCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manifesto:atm:customer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Allows user to update or create customer accounts/balance';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $accountNumber = $this->ask('Enter account number');
        $pin = $this->ask('Enter Pin');
        $totalBalance = $this->ask('Enter starting balance');
        $overdraft = $this->ask('Enter available overdraft amount');

        $valid = !Validator::make([
            'account_number' => $accountNumber,
            'pin' => $pin,
            'account_balance' => $totalBalance,
            'overdraft_available' => $overdraft,
        ], [
            'account_number' => [
                'required',
                'numeric',
                'digits:8',
            ],
            'pin' => [
                'required',
                'numeric',
                'digits:4',
            ],
            'overdraft_available' => [
                'required',
                'numeric',
                'min:0',
            ],
            'account_balance' => [
                'required',
                'numeric',
            ]
        ])->fails();

        if ($valid) {
            $customer = Customer::where(['account_number' => $accountNumber])->first() ?? new Customer();
            $customer->pin = $pin;
            $customer->fill([
                'account_number' => $accountNumber,
                'overdraft_available' => $overdraft,
                'account_balance' => $totalBalance,
            ]);
            $customer->save();
            $this->line('Customer updated/created.');
            dump($customer->getAttributes());
            return 0;
        }

        $this->error('Error validating input values');
        return 1;
    }
}
