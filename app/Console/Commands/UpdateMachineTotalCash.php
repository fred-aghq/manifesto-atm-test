<?php

namespace App\Console\Commands;

use App\Models\Machine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class UpdateMachineTotalCash extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manifesto:atm:cash';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Allows user to modify total cash in ATM.';

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
        $cash = $this->ask('Enter amount of cash ATM should hold');

        $valid = !Validator::make(['cash' => $cash],[
            'cash' => ['numeric','required'],
        ])
        ->fails();

        if ($valid) {
            $machine = Machine::firstOrCreate();
            $machine->total_cash = $cash;
            $machine->save();
            $this->line('Success. Updated cash to ' . $machine->total_cash);
            return 0;
        }

        $this->error('Invalid value');
        return 1;
    }
}
