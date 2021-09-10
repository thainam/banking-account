<?php

namespace BankingAccount\Domain\Account\Exception;

use Exception;

class AccountValidateException extends Exception
{
    /**
     * @codeCoverageIgnore
     */
    public function __construct($message, $code, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
