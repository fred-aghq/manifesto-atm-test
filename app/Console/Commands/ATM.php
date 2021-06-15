<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ATM extends Command
{
    public const ACCOUNT_ERR = 'ACCOUNT_ERR';
    public const FUNDS_ERR = 'FUNDS_ERR';
    public const ATM_ERR = 'ATM_ERR';
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
        $this->line('hello world!');
    }
}
