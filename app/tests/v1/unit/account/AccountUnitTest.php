<?php 

declare(strict_types=1);

use BankingAccount\Domain\Account\Entity\Account;
use BankingAccount\Domain\Account\Exception\AccountValidateException;
use BankingAccount\Domain\Transaction\Exception\TransactionValidateException;
use Tests\TestCase;

final class AccountUnitTest extends TestCase
{
    const NUMBER_ZERO = 0;
    const NUMBER_ONE = 1;
    const EMPTY_VALUE = '';
    const AVAILABLE_BANK_NOTES = [30, 50, 100];

    

    private Account $account;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->account = new Account;
    }

    /**
     * @covers \BankingAccount\Domain\Account\Entity\Account::setId
     * @covers \BankingAccount\Domain\Account\Entity\Account::getId
     */
    public function testShouldPopulateAccountIdWhenValid(): void
    {
        $this->account->setId(self::NUMBER_ONE);

        $this->assertEquals(self::NUMBER_ONE, $this->account->getId());
    }

    /**
     * @covers \BankingAccount\Domain\Account\Entity\Account::setId
     * @covers \BankingAccount\Domain\Account\Entity\Account::getId
     */
    public function testShouldNotPopulateAccountIdWhenNotValid(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->account->setId(self::NUMBER_ZERO);
    }

    /**
     * @covers \BankingAccount\Domain\Account\Entity\Account::setUserId
     * @covers \BankingAccount\Domain\Account\Entity\Account::getUserId
     */
    public function testShouldPopulateUserIdWhenValid(): void
    {
        $this->account->setUserId(self::NUMBER_ONE);

        $this->assertEquals(self::NUMBER_ONE, $this->account->getUserId());
    }

    /**
     * @covers \BankingAccount\Domain\Account\Entity\Account::setUserId
     * @covers \BankingAccount\Domain\Account\Entity\Account::getUserId
     */
    public function testShouldNotPopulateUserIdWhenNotValid(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->account->setUserId(self::NUMBER_ZERO);
    }

    /**
     * @covers \BankingAccount\Domain\Account\Entity\Account::setType
     * @covers \BankingAccount\Domain\Account\Entity\Account::getType
     */
    public function testShouldPopulateAccountTypeWhenValid(): void
    {
        $this->account->setType(self::CHECKING_ACCOUNT_TYPE);

        $this->assertEquals(self::CHECKING_ACCOUNT_TYPE, $this->account->getType());
    }

    /**
     * @covers \BankingAccount\Domain\Account\Entity\Account::setType
     * @covers \BankingAccount\Domain\Account\Entity\Account::getType
     */
    public function testShouldNotPopulateAccountTypeWhenInvalid(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->account->setType(self::EMPTY_VALUE);
        
        $this->expectException('InvalidArgumentException');
        $this->account->setType(self::NULL_VALUE);
    }

    /**
     * @covers \BankingAccount\Domain\Account\Entity\Account::setBalance
     * @covers \BankingAccount\Domain\Account\Entity\Account::getBalance
     */
    public function testShouldPopulateAccountBalanceWhenIsValid(): void
    {
        $this->account->setBalance(self::BALANCE_VALID);

        $this->assertEquals(self::BALANCE_VALID, $this->account->getBalance());
    }

    /**
     * @covers \BankingAccount\Domain\Account\Entity\Account::setBalance
     * @covers \BankingAccount\Domain\Account\Entity\Account::getBalance
     */
    public function testShouldNotPopulateAccountBalanceWhenIsInvalid(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->account->setBalance(self::BALANCE_INVALID);
    }

    /**
     * @covers \BankingAccount\Domain\Account\Entity\Account::fill
     * @covers \BankingAccount\Domain\Account\Entity\Account::setId
     * @covers \BankingAccount\Domain\Account\Entity\Account::setUserId
     * @covers \BankingAccount\Domain\Account\Entity\Account::setType
     * @covers \BankingAccount\Domain\Account\Entity\Account::setBalance
     * @covers \BankingAccount\Domain\Account\Entity\Account::getId
     * @covers \BankingAccount\Domain\Account\Entity\Account::getUserId
     * @covers \BankingAccount\Domain\Account\Entity\Account::getType
     * @covers \BankingAccount\Domain\Account\Entity\Account::getTypeDesc
     * @covers \BankingAccount\Domain\Account\Entity\Account::getBalance
     * @covers \BankingAccount\Domain\Account\Entity\Account::getBalanceBr
     */
    public function testShouldPopulateAllAccountInfo(): void
    {
        $this->account = new Account;

        $accountInfo = new stdClass();
        $accountInfo->id = self::NUMBER_ONE;
        $accountInfo->user_id = self::NUMBER_ONE;;
        $accountInfo->type = self::CHECKING_ACCOUNT_TYPE;
        $accountInfo->balance = self::BALANCE_VALID;

        $balanceBRFormat = number_format(self::BALANCE_VALID, 2, ',', '.');

        $this->account->fill($accountInfo);

        $this->assertEquals(self::NUMBER_ONE, $this->account->getId());
        $this->assertEquals(self::NUMBER_ONE, $this->account->getUserId());
        $this->assertEquals(self::CHECKING_ACCOUNT_TYPE, $this->account->getType());
        $this->assertEquals(self::BALANCE_VALID, $this->account->getBalance());
        $this->assertEquals(self::CHECKING_ACCOUNT_DESC, $this->account->getTypeDesc());
        $this->assertEquals($balanceBRFormat, $this->account->getBalanceBr());
    }

    /**
     * @covers \BankingAccount\Domain\Account\Entity\Account::setBalance
     * @covers \BankingAccount\Domain\Account\Entity\Account::decreaseBalance
     * @covers \BankingAccount\Domain\Account\Entity\Account::getBalanceBr
     */
    public function testShouldNotDecreaseBalanceWhenBalanceIsSmallerThenAmount(): void
    {
        $balance = 200;
        $amount = 500;
        $this->account = new Account;
        $this->account->setBalance($balance);
        
        $this->expectException(AccountValidateException::class);
        $this->account->decreaseBalance($amount);
    }

    /**
     * @covers \BankingAccount\Domain\Account\Entity\Account::setBalance
     * @covers \BankingAccount\Domain\Account\Entity\Account::decreaseBalance
     * @covers \BankingAccount\Helper\Atm::checkAmountCents
     */
    public function testShouldNotDecreaseBalanceWhenAmountHasCents(): void
    {
        $balance = 200;
        $amount = 30.45;
        $this->account = new Account;
        $this->account->setBalance($balance);
        
        $this->expectException(TransactionValidateException::class);
        $this->account->decreaseBalance($amount);
    }

    
    /**
     * @covers \BankingAccount\Domain\Account\Entity\Account::setBalance
     * @covers \BankingAccount\Domain\Account\Entity\Account::decreaseBalance
     * @covers \BankingAccount\Domain\Account\Entity\Account::getBalance
     * @covers \BankingAccount\Helper\Atm::checkAmountCents
     */
    public function testShouldDecreaseBalance(): void
    {
        $balance = 200;
        $amount = 190;
       
        
        $this->account = new Account;
        $this->account->setBalance($balance);
        $this->account->decreaseBalance($amount);

        $expected = $balance - $amount;

        $this->assertEquals($expected, $this->account->getBalance());
    }
}
