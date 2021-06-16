<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Machine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (Machine::all()->count() > 0) {
            DB::table((new Machine())->getTable())->truncate();
        }

        Machine::factory()->create();

        Customer::factory([
            'account_number' => 12345678,
            'pin' => 1234,
            'account_balance' => 500,
            'overdraft_available' => 100,
        ])
        ->create();
    }
}
