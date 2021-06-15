<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ATM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manfiesto:atm';

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
