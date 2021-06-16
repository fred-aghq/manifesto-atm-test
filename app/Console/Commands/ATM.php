<?php

namespace App\Console\Commands;

use App\Services\ATM\MachineService;
use App\Services\ATM\MachineServiceInterface;
use Illuminate\Console\Command;

/**
 * The CLI entrypoint for the ATM service.
 * @package App\Console\Commands
 */
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
        $this->line('hello world!');
    }
}
