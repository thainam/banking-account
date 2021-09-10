<?php 

declare(strict_types=1);

namespace BankingAccount\Domain\Transaction\Entity;

use BankingAccount\Domain\Transaction\Exception\TransactionValidateException;
use BankingAccount\Helper\Atm;
use BankingAccount\Helper\ErrorHandler;
use BankingAccount\Helper\HttpStatus;
use DateTime;
use JsonSerializable;
use stdClass;

class Transaction implements ITransaction, JsonSerializable
{
    const WITHDRAW_OPERATION = 'W';

    const DEPOSIT_OPERATION = 'D';

    const WITHDRAW_OPERATION_DESC = 'Saque';

    const DEPOSIT_OPERATION_DESC = 'Depósito';

    const LIST_CACHE_KEY = 'user_%d_account_%d_transactions';

    private int $id;

    private int $accountId;

    private string $operation;

    private float $amount;

    private string $createdAt;

    private array $banknotes;

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

    public function setId(?int $id): void
    {
        if (! $id) {
            throw new TransactionValidateException('ID da transação inválido!', HttpStatus::CODE_422);
        }
        $this->id = $id;
    }

    public function getAccountId(): int
    {
        return $this->accountId;
    }

    public function setAccountId(?int $accountId): void
    {
        if (! $accountId) {
            throw new TransactionValidateException('ID da conta bancária inválido!', HttpStatus::CODE_422);
        }
        $this->accountId = $accountId;
    }

    public function getOperation(): string
    {
       return $this->operation;
    }

    public function setOperation(?string $operation): void
    {
        $operation = $operation ? mb_strtoupper($operation) : null;

        if (! in_array($operation, [self::WITHDRAW_OPERATION, self::DEPOSIT_OPERATION])) {
            throw new TransactionValidateException('Operação inválida!', HttpStatus::CODE_422);
        }

        $this->operation = $operation;
    }

    public function getOperationDesc(): string
    {
       return 
       $this->operation == self::WITHDRAW_OPERATION  
       ? self::WITHDRAW_OPERATION_DESC 
       :  self::DEPOSIT_OPERATION_DESC;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        Atm::checkAmountCents($amount);
        
        switch ($this->operation) {
            case self::DEPOSIT_OPERATION:
                $this->validateDeposit($amount);
                break;
            case self::WITHDRAW_OPERATION:
                $this->validateWithdraw($amount);
                break;
        }
        
        $this->amount = $amount;
    }

    public function validateDeposit(float $amount): void
    {
        if ($amount < Atm::MIN_DEPOSIT_AMOUNT ) {
            $message = 'Valor inválido, informe um valor maior ou igual a '.Atm::MIN_DEPOSIT_AMOUNT;
            throw new TransactionValidateException($message, HttpStatus::CODE_422);
        }
    }
    
    public function validateWithdraw(float $amount): void
    {
       Atm::checkMinimumAmount($amount);
    }

    public function getAmountBr(): string
    {
        return number_format($this->amount, 2, ',', '.');
    }

    public function setCreatedAt(string $createdAt): void
    {
        $format = 'Y-m-d H:i:s';
        $createdAt = DateTime::createFromFormat($format, $createdAt);
        if (! $createdAt) {
            $message = ErrorHandler::INVALID_TRANSACTION_CREATED_AT_MESSAGE;
            throw new TransactionValidateException($message, HttpStatus::CODE_422);
        }

        $this->createdAt = $createdAt->format($format);
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getCreatedAtBr(): string
    {
        $createdAt = DateTime::createFromFormat('Y-m-d H:i:s', $this->createdAt);
        return $createdAt->format('d/m/Y H:i:s');
    }

    public function setBanknotes(array $banknotes): void
    {
        $this->banknotes = array_filter($banknotes);
    }

    public function getBanknotes(): array
    {
       return $this->banknotes ?? [];
    }

    public function getFormattedBanknotes(): array
    {
        $banknotes = $this->banknotes ?? [];

        foreach ($banknotes as $note => $qty) {

            if ($qty <= Atm::ZERO) {
                 continue;
            }

            $plural = $qty > Atm::ONE ? 's' : '';

            $formattedBanknotes[$note] = "{$qty} nota{$plural} de R$ {$note}";

        }

        return $formattedBanknotes ?? [];
    }

    public function fill(stdClass $transactionInfo): void
    {
        if (isset($transactionInfo->id)) {
            $this->setId((int) $transactionInfo->id);
        }

        $this->setAccountId((int) $transactionInfo->account_id);
        $this->setOperation($transactionInfo->operation);
        $this->setAmount((float) $transactionInfo->amount);

        if (isset($transactionInfo->created_at)) {
            $this->setCreatedAt($transactionInfo->created_at);
        }
        
        if (isset($transactionInfo->banknotes)) {
            if (is_string($transactionInfo->banknotes)) {
                $transactionInfo->banknotes = json_decode($transactionInfo->banknotes, true);
            }
            $this->setBanknotes($transactionInfo->banknotes);
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'account_id' => $this->getAccountId(),
            'operation' => $this->getOperation(),
            'operation_desc' => $this->getOperationDesc(),
            'amount' => $this->getAmount(),
            'amount_br' => $this->getAmountBr(),
            'created_at' => $this->getCreatedAt(),
            'created_at_br' => $this->getCreatedAtBr(),
            'banknotes' => $this->getBanknotes(),
            'banknotes_br' => $this->getFormattedBanknotes(),
        ];
    }
}
