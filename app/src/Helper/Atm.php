<?php

declare(strict_types=1);

namespace BankingAccount\Helper;

use BankingAccount\Domain\Transaction\Exception\TransactionValidateException;

/**
 * Class to help with Atm (Automated Teller Machine) validations.
 */
class Atm
{
    const ZERO = 0;
    const ONE = 1;
    const MIN_DEPOSIT_AMOUNT = 0;
    
    private static array $amountPerBanknote;

    private static array $availableBanknotes = [20, 50, 100];

    private static int $lowestBanknote;

    private static float $amount;

    /**
     * Method to get the banknotes ordered by highest.
     * 
     * @param float $amount
     * @return array|null
     */
    public static function getBanknotes(float $amount): ?array
    {
        self::$amount = $amount;

        self::$lowestBanknote = min(self::$availableBanknotes);

        self::checkMinimumAmount(self::$amount);

        rsort(self::$availableBanknotes, SORT_NUMERIC);

        foreach (self::$availableBanknotes as $banknote) {

            self::$amountPerBanknote[$banknote] = self::ZERO;

            self::subtract($banknote);
        }
        
        self::checkAmount();
        
        return self::$amountPerBanknote ?? null;
    }

    /**
     * Method to check if amount is higher 
     * then lowest bank note.
     * 
     * @throws TransactionValidateException
     * @return void
     */
    public static function checkMinimumAmount(float $amount): void
    {
        $lowestBanknotes = min(self::$availableBanknotes);
        if ($amount < $lowestBanknotes) {
            $minAmount = $lowestBanknotes;
            $message = "O valor mínimo para saque é de R$ {$minAmount},00";
            throw new TransactionValidateException($message, HttpStatus::CODE_422);
        }
    }

    /**
     * Method to check if the subtraction can happen
     * without affect next substractions needed.
     * 
     * @param int $banknote
     * @return bool
     */
    private static function isSubtractable(int $banknote): bool
    {
        $subtraction = self::$amount - $banknote;
       
        if ($subtraction < self::ZERO) {
            return false;
        }
        
        if ($subtraction > self::ZERO && $subtraction < self::$lowestBanknote) {
            return false;
        }
        
         if ($subtraction > $banknote) {
            return true;
        }

        foreach (self::$availableBanknotes as $note) {
            
            if ($note > $banknote) {
                continue;
            }
            
            $remainder = $subtraction % $note;
            
            $hasInArray = in_array($remainder, self::$availableBanknotes);

            $remainderFromMinimumBanknote = $remainder % self::$lowestBanknote == self::ZERO;
            
            if ($remainder == self::ZERO || $hasInArray || $remainderFromMinimumBanknote) {
                return true;
            }
        }

        return false;
    }

    /**
     * Method to make recursive subtractions 
     * until is possible with each banknote.
     * 
     * @param int $banknote
     * @return self
     */
    private static function subtract(int $banknote): ?self
    {
        if (self::isSubtractable($banknote) && self::$amount > self::ZERO) {
            
            self::$amount = self::$amount - $banknote;
            
            self::$amountPerBanknote[$banknote]++;
            
            return self::subtract($banknote);
        }
        return null;
    }

    /**
     * Method to check if still remains some amount
     * to calculate.
     * 
     * @throws TransactionValidateException
     * @return void
     */
    private static function checkAmount(): void
    {
        if (self::$amount > self::ZERO) {
            $banknotes = implode(', ', self::$availableBanknotes);
            $message = "Cédulas indisponíveis para esse valor. Cédulas disponíveis: {$banknotes}.";
            throw new TransactionValidateException($message, HttpStatus::CODE_422);
        }
    }

    /**
     * Method to check if an amount has cents.
     * 
     * @throws TransactionValidateException
     * @return void
     */
    public static function checkAmountCents($amount): void
    {
        $amountInteger = floor($amount);
        $amountCents = $amount - $amountInteger;
        
        if ($amountCents > Atm::ZERO) {
            $message = 'Não são permitidos centavos, informe um valor inteiro.';
            throw new TransactionValidateException($message, HttpStatus::CODE_422);
        }
    }
}
