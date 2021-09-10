<?php

namespace BankingAccount\Exception;

use Exception;

class BankingAccountException extends Exception
{
    /**
     * @codeCoverageIgnore
     */
    public function __construct($message, $code, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}
