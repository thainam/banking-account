<?php 

declare(strict_types=1);

namespace BankingAccount\Domain\Account\Entity;

use BankingAccount\Domain\Account\Exception\AccountValidateException;
use BankingAccount\Helper\Atm;
use BankingAccount\Helper\HttpStatus;
use InvalidArgumentException;
use JsonSerializable;
use stdClass;

class Account implements IAccount, JsonSerializable
{
    const CHECKING_ACCOUNT_TYPE = 'C';
    const SAVINGS_ACCOUNT_TYPE = 'S';

    const CHECKING_ACCOUNT_DESC = 'Conta Corrente';
    const SAVINGS_ACCOUNT_DESC = 'Conta Poupança';

    const MIN_BALANCE_RANGE = 0;
    
    private int $id;
    private int $userId;
    private string $type;
    private float $balance;
    private array $bankNotes;

    /**
     * @codeCoverageIgnore
     */
    public function __construct(int $id = null)
    {
        if ($id) {
            $this->setId($id);
        }
    }

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function setId(int $id): void
    {
        if (! $id) {
            throw new InvalidArgumentException('ID da conta inválido!', HttpStatus::CODE_422);
        }
        $this->id = $id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        if (! $userId) {
            throw new InvalidArgumentException('ID do usuário inválido!', HttpStatus::CODE_422);
        }
        $this->userId = $userId;
    }

    public function getType(): string
    {
       return $this->type;
    }

    public function setType(?string $type): void
    {
        $type = $type ? mb_strtoupper($type) : null;
        
        if (! in_array($type, [self::CHECKING_ACCOUNT_TYPE, self::SAVINGS_ACCOUNT_TYPE])) {
            throw new InvalidArgumentException('Tipo de conta inválido!', HttpStatus::CODE_422);
        }
        $this->type = $type;
    }

    public function getTypeDesc(): string
    {
       return $this->type == self::CHECKING_ACCOUNT_TYPE ? self::CHECKING_ACCOUNT_DESC : self::SAVINGS_ACCOUNT_DESC;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): void
    {
        if ($balance < self::MIN_BALANCE_RANGE ) {
            $message = 'Valor do saldo inválido, informe um valor maior ou igual a '.self::MIN_BALANCE_RANGE;
            throw new InvalidArgumentException($message, HttpStatus::CODE_422);
        }
        $this->balance = $balance;
    }

    public function getBalanceBr(): string
    {
        return number_format($this->balance, 2, ',', '.');
    }

    public function decreaseBalance(float $amount): void
    {
        if ($amount > $this->balance) {
            $message = "Saldo insuficiente para esta operação. Saldo disponível: R$ {$this->getBalanceBr()}";
            throw new AccountValidateException($message, HttpStatus::CODE_422);
        }

        Atm::checkAmountCents($amount);
        
        $this->balance -= $amount;
    }

    public function increaseBalance(float $amount): void
    {
        if ($amount < Atm::MIN_DEPOSIT_AMOUNT ) {
            $message = 'Valor inválido, informe um valor maior ou igual a '.Atm::MIN_DEPOSIT_AMOUNT;
            throw new AccountValidateException($message, HttpStatus::CODE_422);
        }

        Atm::checkAmountCents($amount);

        $this->balance += $amount;
    }

    public function fill(stdClass $accountInfo): void
    {
        if (isset($accountInfo->id)) {
            $this->setId((int) $accountInfo->id);
        }

        $this->setUserId((int) $accountInfo->user_id);
        $this->setType($accountInfo->type);
        $this->setBalance((float) $accountInfo->balance);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'user_id' => $this->getUserId(),
            'type' => $this->getType(),
            'type_desc' => $this->getTypeDesc(),
            'balance' => $this->getBalance(),
            'balance_br' => $this->getBalanceBr(),
        ];
    }

}
