<?php

namespace BankingAccount\Domain\Transaction\Exception;

use Exception;

class TransactionValidateException extends Exception
{
    /**
     * @codeCoverageIgnore
     */
    public function __construct($message, $code, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
