<?php

namespace App\Repositories;

use App\Models\Customer;
use Orkhanahmadov\EloquentRepository\EloquentRepository;

class CustomerRepository extends EloquentRepository
{
     protected $entity = Customer::class;

     public function findByAccountNumber(int $accountNumber) {
         return $this->getWhereFirst('account_number', $accountNumber);
     }
}
