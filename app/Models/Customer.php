<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $hidden = ['pin'];

    public function getTotalFundsAvailableAttribute()
    {
        return $this->account_balance + $this->overdraft_available;
    }
}
