<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Machine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Reset application state to initial spec conditions
     *
     * @return void
     */
    public function run()
    {
        Customer::whereIn('account_number', [12345678, 87654321])->delete();

        Customer::factory([
            'account_number' => 12345678,
            'pin' => 1234,
            'account_balance' => 100,
            'overdraft_available' => 0,
        ])
        ->create();

        Customer::factory([
            'account_number' => 87654321,
            'pin' => 4321,
            'account_balance' => 500,
            'overdraft_available' => 100,
        ])
        ->create();
    }
}
