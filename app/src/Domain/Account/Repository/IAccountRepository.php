<?php 

declare(strict_types=1);

namespace BankingAccount\Domain\Account\Repository;

use BankingAccount\Domain\Account\Entity\IAccount;
use BankingAccount\Domain\User\Entity\IUser;

/**
 * @codeCoverageIgnore
 */
interface IAccountRepository
{
    public function getAccountByUser(IUser $user): array;
    public function getByIdAndUserId(int $id, int $userId): mixed;
    public function create(IAccount $account): IAccount;
    public function updateBalance(IAccount $account): void;
    public function checkUserNotEmptyBalanceAccounts(IUser $user): bool;
}
