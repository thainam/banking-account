<?php 

declare(strict_types=1);

namespace BankingAccount\Domain\Account\Entity;

use stdClass;

/**
 * @codeCoverageIgnore
 */
interface IAccount
{
    public function getId(): ?int;
    public function setId(int $id): void;
    
    public function getUserId(): ?int;
    public function setUserId(int $userId): void;

    public function getType(): string;
    public function setType(string $type): void;
    public function getTypeDesc(): string;

    public function getBalance(): float;
    public function setBalance(float $balance): void;
    public function getBalanceBr(): string;

    public function increaseBalance(float $amount): void;
    
    public function decreaseBalance(float $amount): void;

    public function fill(stdClass $accountInfo): void;
}
