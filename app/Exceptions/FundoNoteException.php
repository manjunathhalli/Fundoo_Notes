<?php

namespace App\Exceptions;

use Exception;

class FundoNoteException extends Exception
{
    public function message(){
        return $this->getMessage();

    }
    public function statusCode(){
        return $this->getCode();
    }
} 