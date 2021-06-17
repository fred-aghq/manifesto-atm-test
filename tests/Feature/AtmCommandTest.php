<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Machine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AtmCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        DB::table('machines')->truncate();
        DB::table('customers')->truncate();
        parent::tearDown();
    }
}
