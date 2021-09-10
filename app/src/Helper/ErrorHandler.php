<?php 

declare(strict_types=1);

namespace BankingAccount\Helper;

use BankingAccount\Domain\Account\Entity\Account;

/**
 * Class responsible to handle errors and return it 
 * with correct format.
 */
class ErrorHandler
{
    const DEFAULT_MESSAGE = 'Oops.. Ocorreu um erro na operação, tente novamente!';

    const DUPLICATED_USER_MESSAGE = 'Não é possível realizar esta operação pois este CPF já está sendo utilizado!';
    const NOT_FOUND_USER_MESSAGE = 'Usuário não encontrado.';
    const INVALID_USER_BIRTHDATE_MESSAGE = 'Data de nascimento inválida. Envie no formato: dd/mm/yyyy';
    
    const USER_ACCOUNT_BALANCE_NOT_EMPTY_MESSAGE = 
    'Não foi possível deletar o usuário pois ele possui uma ou mais contas com saldo positivo.';
    
    const ACCOUNT_NOT_FOUND_MESSAGE = 'Conta bancária não encontrada.';
    const ACCOUNT_DUPLICATED_MESSAGE = [
        Account::CHECKING_ACCOUNT_TYPE => 'Este usuário já possui uma conta corrente.',
        Account::SAVINGS_ACCOUNT_TYPE => 'Este usuário já possui uma conta poupança.'
    ];

    const INVALID_TRANSACTION_CREATED_AT_MESSAGE = 'Data da transação inválida.';
    
    /**
     * @param int $code
     * @param string $message
     * 
     * @return array
     */
    public static function handle(int $code = null, string $message = null): array
    {
        $message = $message ?? self::DEFAULT_MESSAGE;
        $code = $code ?? HttpStatus::CODE_503;

        return ['errors' => ['code' => $code, 'message' => $message]];
    }
}
