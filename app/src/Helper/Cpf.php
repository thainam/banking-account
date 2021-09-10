<?php

declare(strict_types=1);

namespace BankingAccount\Helper;

/**
 * Class to check if CPF is valid.
 */
class Cpf
{
    /**
     * @param string $cpf
     * @return bool
     */
    public static function validate(?string $cpf): bool
    {
        $cpf = $cpf ?? '';
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );

        if (strlen($cpf) != 11) {
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    /**
     * Method to mask CPF number.
     * 
     * @param string $cpf
     * @return string
     */
    public static function doMask(string $cpf): string
    {
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
        return substr($cpf, 0, 3).'.'.substr($cpf, 3, 3).'.'.substr($cpf, 6, 3).'-'.substr($cpf, 9, 2);
    }
}
