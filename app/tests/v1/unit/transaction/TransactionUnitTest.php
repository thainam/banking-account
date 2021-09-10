<?php 

declare(strict_types=1);

use BankingAccount\Domain\Transaction\Exception\TransactionValidateException;
use BankingAccount\Domain\Transaction\Entity\Transaction;
use Tests\TestCase;

final class TransactionUnitTest extends TestCase
{
    const NUMBER_ZERO = 0;
    const NUMBER_ONE = 1;
    const AVAILABLE_BANK_NOTES = [30, 50, 100];

    private Transaction $transaction;

    public function setUp(): void
    {
        parent::setUp();
        $this->transaction = new Transaction;
    }

    /**
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setId
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getId
     */
    public function testShouldPopulateTransactionIdWhenValid(): void
    {
        $this->transaction->setId(self::NUMBER_ONE);

        $this->assertEquals(self::NUMBER_ONE, $this->transaction->getId());
    }

    /**
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setId
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getId
     */
    public function testShouldNotPopulateTransactionIdWhenNotValid(): void
    {
        $this->expectException(TransactionValidateException::class);
        $this->transaction->setId(self::NUMBER_ZERO);
    }

    /**
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setAccountId
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getAccountId
     */
    public function testShouldPopulateAccountIdWhenValid(): void
    {
        $this->transaction->setAccountId(self::NUMBER_ONE);

        $this->assertEquals(self::NUMBER_ONE, $this->transaction->getAccountId());
    }

    /**
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setAccountId
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getAccountId
     */
    public function testShouldNotPopulateAccountIdWhenNotValid(): void
    {
        $this->expectException(TransactionValidateException::class);
        $this->transaction->setAccountId(self::NUMBER_ZERO);
        
        $this->expectException(TransactionValidateException::class);
        $this->transaction->setAccountId(self::NULL_VALUE);
    }

    /**
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setOperation
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getOperation
     */
    public function testShouldPopulateTransactionOperationWhenValid(): void
    {
        $this->transaction->setOperation(self::DEPOSIT_OPERATION);

        $this->assertEquals(self::DEPOSIT_OPERATION, $this->transaction->getOperation());
    }

    /**
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setOperation
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getOperation
     */
    public function testShouldNotPopulateTransactionOperationWhenInvalid(): void
    {
        $this->expectException(TransactionValidateException::class);
        $this->transaction->setOperation(self::EMPTY_VALUE);
        $this->transaction->setOperation(null);
    }

    /**
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setAmount
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getAmount
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setOperation
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::validateDeposit
     * @covers \BankingAccount\Helper\Atm::checkAmountCents
     */
    public function testShouldPopulateTransactionAmountWhenIsValid(): void
    {
        $this->transaction->setOperation(self::DEPOSIT_OPERATION);
        $this->transaction->setAmount(self::BALANCE_VALID);

        $this->assertEquals(self::BALANCE_VALID, $this->transaction->getAmount());
    }

    /**
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setAmount
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getAmount
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setOperation
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::validateWithdraw
     * @covers \BankingAccount\Helper\Atm::checkAmountCents
     * @covers \BankingAccount\Helper\Atm::checkMinimumAmount
     */
    public function testShouldNotPopulateTransactionAmountWhenIsLowerThenMinimumBanknote(): void
    {
        $amount = 10;
        $this->transaction->setOperation(self::WITHDRAW_OPERATION);

        $this->expectException(TransactionValidateException::class);
        $this->transaction->setAmount($amount);
    }

    /**
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setAmount
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getAmount
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setOperation
     * @covers \BankingAccount\Helper\Atm::checkAmountCents   
     */
    public function testShouldNotPopulateTransactionDepositAmountWhenHasCents(): void
    {
        $amount = 30.45;
        $this->transaction->setOperation(self::DEPOSIT_OPERATION);

        $this->expectException(TransactionValidateException::class);
        $this->transaction->setAmount($amount);
    }

    /**
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setAmount
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getAmount
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setOperation
     * @covers \BankingAccount\Helper\Atm::checkAmountCents   
     */
    public function testShouldNotPopulateTransactionWithdrawalAmountWhenHasCents(): void
    {
        $amount = 30.45;
        $this->transaction->setOperation(self::WITHDRAW_OPERATION);

        $this->expectException(TransactionValidateException::class);
        $this->transaction->setAmount($amount);
    }

    /**
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setAmount
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getAmount
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setCreatedAt
     */
    public function testShouldNotPopulateTransactionCreatedAtWhenDateIsInvalid(): void
    {
        $createdAt = '213467';
        $this->expectException(TransactionValidateException::class);
        $this->transaction->setCreatedAt($createdAt);
    }

    /**
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setCreatedAt
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getCreatedAt
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getCreatedAtBr
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setCreatedAt
     */
    public function testShouldPopulateTransactionCreatedAtWhenDateIsValid(): void
    {
        $createdAt = '2021-09-08 21:00:45';
        $createdAtBr = '08/09/2021 21:00:45';
        $this->transaction->setCreatedAt($createdAt);
        $this->assertEquals($createdAt, $this->transaction->getCreatedAt());
        $this->assertEquals($createdAtBr, $this->transaction->getCreatedAtBr());
    }

    /**
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::fill
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setId
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setAccountId
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setOperation
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setAmount
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getId
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getAccountId
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getOperation
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getOperationDesc
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getAmount
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getAmountBr
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getCreatedAt
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getCreatedAtBr
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setCreatedAt
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::validateDeposit
     * @covers \BankingAccount\Helper\Atm::checkAmountCents
     */
    public function testShouldPopulateAllTransactionInfo(): void
    {
        $this->transaction = new Transaction();

        $createdAt = '2021-09-08 21:00:45';
        $createdAtBr = '08/09/2021 21:00:45';

        $transactionInfo = new stdClass();
        $transactionInfo->id = self::NUMBER_ONE;
        $transactionInfo->account_id = self::NUMBER_ONE;
        $transactionInfo->operation = self::DEPOSIT_OPERATION;
        $transactionInfo->amount = self::BALANCE_VALID;
        $transactionInfo->created_at = $createdAt;

        $balanceBRFormat = number_format(self::BALANCE_VALID, 2, ',', '.');

        $this->transaction->fill($transactionInfo);

        $this->assertEquals($transactionInfo->id, $this->transaction->getId());
        $this->assertEquals($transactionInfo->account_id, $this->transaction->getAccountId());
        $this->assertEquals(self::DEPOSIT_OPERATION, $this->transaction->getOperation());
        $this->assertEquals(self::DEPOSIT_OPERATION_DESC, $this->transaction->getOperationDesc());
        $this->assertEquals($transactionInfo->amount, $this->transaction->getAmount());
        $this->assertEquals($transactionInfo->created_at, $this->transaction->getCreatedAt());
        $this->assertEquals($createdAtBr, $this->transaction->getCreatedAtBr());
        $this->assertEquals($balanceBRFormat, $this->transaction->getAmountBr());
    }
}
