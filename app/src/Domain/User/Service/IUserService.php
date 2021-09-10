<?php 

namespace BankingAccount\Domain\User\Service;

use BankingAccount\Domain\User\Entity\IUser;

/**
 * @codeCoverageIgnore
 */
interface IUserService
{
    public function list(): array;
    public function search(string $term): array;
    public function create(): IUser;
    public function update(int $id, array $fields): IUser;
    public function delete(int $id): void;
    public function formatToSerializable(array $users): array;
}
