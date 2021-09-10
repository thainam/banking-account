<?php 

namespace BankingAccount\Domain\Transaction\Service;

use BankingAccount\Domain\Account\Entity\IAccount;
use BankingAccount\Domain\Transaction\Entity\ITransaction;

/**
 * @codeCoverageIgnore
 */
interface ITransactionService
{
    public function list(int $userId, int $accountId): array;
    public function create(int $userId, int $accountId, array $fields): ITransaction;
    public function deposit(IAccount $account, ITransaction $transaction): ITransaction;
    public function withdraw(IAccount $account, ITransaction $transaction): ITransaction;
    public function formatToSerializable(array $transactions): array;
}
