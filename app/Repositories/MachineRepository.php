<?php

namespace App\Repositories;

use App\Models\Machine;
use Orkhanahmadov\EloquentRepository\EloquentRepository;

class MachineRepository extends EloquentRepository
{
     protected $entity = Machine::class;

     public function getMachine()
     {
         return $this->all()->first();
     }
}
