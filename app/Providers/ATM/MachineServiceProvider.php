<?php

namespace App\Providers\ATM;

use App\Services\ATM\MachineService;
use App\Services\ATM\MachineServiceInterface;
use Illuminate\Support\ServiceProvider;

class MachineServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(MachineServiceInterface::class, MachineService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
