<?php

namespace BankingAccount\Domain\User\Exception;

use Exception;

class UserValidateException extends Exception
{
    /**
     * @codeCoverageIgnore
     */
    public function __construct($message, $code, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}
