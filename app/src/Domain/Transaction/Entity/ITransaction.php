<?php 

declare(strict_types=1);

namespace BankingAccount\Domain\Transaction\Entity;

use stdClass;

/**
 * @codeCoverageIgnore
 */
interface ITransaction
{
    public function getId(): ?int;
    public function setId(int $id): void;
    
    public function getAccountId(): ?int;
    public function setAccountId(int $accountId): void;

    public function getOperation(): string;
    public function setOperation(string $operation): void;
    public function getOperationDesc(): string;

    public function getAmount(): float;
    public function setAmount(float $amount): void;

    public function getAmountBr(): string;

    public function validateDeposit(float $amount): void;
    public function validateWithdraw(float $amount): void;

    public function getCreatedAt(): string;
    public function setCreatedAt(string $createdAt): void;

    public function getCreatedAtBr(): string;

    public function getBanknotes(): array;
    public function setBanknotes(array $amount): void;
    public function getFormattedBanknotes(): array;

    public function fill(stdClass $transactionInfo): void;
}
