<?php 

namespace BankingAccount\Domain\Account\Service;

use BankingAccount\Domain\Account\Entity\IAccount;

/**
 * @codeCoverageIgnore
 */
interface IAccountService
{
    public function list(int $userId): array;
    public function create(int $userId, array $fields): IAccount;
    public function formatToSerializable(array $userAccounts): array;
}
