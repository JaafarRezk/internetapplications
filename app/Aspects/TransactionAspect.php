<?php

namespace App\Aspects;

use Illuminate\Support\Facades\DB;

class TransactionAspect extends Aspect
{

    public function before(){
        DB::beginTransaction();
    }
    public function after(){
        DB::commit();
    }
    public function exception(){
        DB::rollBack();
    }

}

