<?php 

declare(strict_types=1);

namespace BankingAccount\Domain\Transaction\Repository;

use BankingAccount\Domain\Account\Entity\IAccount;
use BankingAccount\Domain\Transaction\Entity\ITransaction;

/**
 * @codeCoverageIgnore
 */
interface ITransactionRepository
{
    public function getByAccountId(IAccount $account): array;
    public function deposit(IAccount $account, ITransaction $transaction): ITransaction;
    public function withdraw(IAccount $account, ITransaction $transaction): ITransaction;
}
