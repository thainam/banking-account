<?php 

declare(strict_types=1);

namespace BankingAccount\Domain\User\Repository;

use BankingAccount\Domain\User\Entity\IUser;

/**
 * @codeCoverageIgnore
 */
interface IUserRepository
{
    public function getAll(): array;
    public function getById(int $id): mixed;
    public function getByTerm(string $param): array;
    public function create(IUser $user): IUser;
    public function update(IUser $user): IUser;
    public function delete(IUser $user): int;
}
