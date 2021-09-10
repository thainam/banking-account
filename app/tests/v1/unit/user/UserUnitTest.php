<?php 

declare(strict_types=1);

use BankingAccount\Domain\User\Entity\User;
use Tests\RandomCpf;
use Tests\TestCase;

final class UserUnitTest extends TestCase
{
    /**
     * @covers \BankingAccount\Domain\User\Entity\User::setId
     * @covers \BankingAccount\Domain\User\Entity\User::getId
     */
    public function testShouldPopulateUserIdWhenNotEmpty(): void
    {
        $id = 1;
        $user = new User;
        $user->setId($id);

        $this->assertEquals(
            $id,
            $user->getId()
        );
    }

    /**
     * @covers \BankingAccount\Domain\User\Entity\User::setId
     * @covers \BankingAccount\Domain\User\Entity\User::getId
     */
    public function testShouldNotPopulateUserIdWhenNotValid(): void
    {
        $id = 0;
        $user = new User;

        $this->expectException('InvalidArgumentException');
        $user->setId($id);
    }

    /**
     * @covers \BankingAccount\Domain\User\Entity\User::setName
     * @covers \BankingAccount\Domain\User\Entity\User::getName
     */
    public function testShouldPopulateUserNameWhenNotEmpty(): void
    {
        $name = 'João';
        $user = new User;
        $user->setName($name);

        $this->assertEquals(
            $name,
            $user->getName()
        );
    }

    /**
     * @covers \BankingAccount\Domain\User\Entity\User::setName
     * @covers \BankingAccount\Domain\User\Entity\User::getName
     */
    public function testShouldNotPopulateUserNameWhenEmpty(): void
    {
        $name = '';
        $user = new User;

        $this->expectException('InvalidArgumentException');
        $user->setName($name);
    }

    /**
     * @covers \BankingAccount\Domain\User\Entity\User::setName
     * @covers \BankingAccount\Domain\User\Entity\User::getName
     */
    public function testShouldNotPopulateUserNameWhenNull(): void
    {
        $user = new User;

        $this->expectException('InvalidArgumentException');
        $user->setName(self::NULL_VALUE);
    }

    /**
     * @covers \BankingAccount\Domain\User\Entity\User::setCpf
     * @covers \BankingAccount\Domain\User\Entity\User::getCpf
     * @covers \BankingAccount\Helper\Cpf::validate
     * @covers \BankingAccount\Helper\Cpf::doMask
     */
    public function testShouldPopulateUserCpfWhenIsValid(): void
    {
        $cpf = RandomCpf::generate();
        $user = new User;
        $user->setCpf($cpf);

        $this->assertEquals(
            $cpf,
            $user->getCpf()
        );
    }

    /**
     * @covers \BankingAccount\Domain\User\Entity\User::setCpf
     * @covers \BankingAccount\Domain\User\Entity\User::getCpf
     * @covers \BankingAccount\Helper\Cpf::validate
     * @covers \BankingAccount\Helper\Cpf::doMask
     */
    public function testShouldNotPopulateUserCpfWhenIsNull(): void
    {
        $user = new User;

        $this->expectException('InvalidArgumentException');
        $user->setCpf(self::NULL_VALUE);
    }

    /**
     * @covers \BankingAccount\Domain\User\Entity\User::setCpf
     * @covers \BankingAccount\Domain\User\Entity\User::getCpf
     * @covers \BankingAccount\Helper\Cpf::validate
     * @covers \BankingAccount\Helper\Cpf::doMask
     */
    public function testShouldNotPopulateUserCpfWhenIsInvalid(): void
    {
        $cpf = '';
        $user = new User;

        $this->expectException('InvalidArgumentException');
        $user->setCpf($cpf);
    }

    /**
     * @covers \BankingAccount\Domain\User\Entity\User::setBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdate
     */
    public function testShouldPopulateUserBirthdateWhenIsUsFormat(): void
    {
        $birthdate = '1990-08-23';
        $user = new User;
        $user->setBirthdate($birthdate);

        $this->assertEquals(
            $birthdate,
            $user->getBirthdate()
        );
    }

    /**
     * @covers \BankingAccount\Domain\User\Entity\User::setBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdateBr
     */
    public function testShouldPopulateUserBirthdateWhenIsBrFormat(): void
    {
        $birthdate = '23/08/1990';
        $user = new User;
        $user->setBirthdate($birthdate);

        $this->assertEquals(
            $birthdate,
            $user->getBirthdateBr()
        );
    }

    /**
     * @covers \BankingAccount\Domain\User\Entity\User::setBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdate
     */
    public function testShouldNotPopulateUserBirthdateIsEmpty(): void
    {
        $birthdate = '';
        $user = new User;

        $this->expectException('InvalidArgumentException');
        $user->setBirthdate($birthdate);
    }

    /**
     * @covers \BankingAccount\Domain\User\Entity\User::setBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdate
     */
    public function testShouldNotPopulateUserBirthdateIsNull(): void
    {
        $user = new User;

        $this->expectException('InvalidArgumentException');
        $user->setBirthdate(self::NULL_VALUE);
    }

    /**
     * @covers \BankingAccount\Domain\User\Entity\User::setBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdateBr
     */
    public function testShouldRetrieveUserBirthdateBrFormat(): void
    {
        $birthdate = '1990-08-23';
        $birthdateBr = '23/08/1990';
        $user = new User;
        $user->setBirthdate($birthdate);

        $this->assertEquals(
            $birthdateBr,
            $user->getBirthdateBr()
        );
    }

    /**
     * @covers \BankingAccount\Domain\User\Entity\User::fill
     * @covers \BankingAccount\Domain\User\Entity\User::setId
     * @covers \BankingAccount\Domain\User\Entity\User::setName
     * @covers \BankingAccount\Domain\User\Entity\User::setCpf
     * @covers \BankingAccount\Domain\User\Entity\User::setBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::getId
     * @covers \BankingAccount\Domain\User\Entity\User::getName
     * @covers \BankingAccount\Domain\User\Entity\User::getCpf
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdate
     * @covers \BankingAccount\Helper\Cpf::validate
     * @covers \BankingAccount\Helper\Cpf::doMask
     */
    public function testShouldPopulateAllUserInfo(): void
    {
        $userInfo = new stdClass();
        $userInfo->id = 1;
        $userInfo->name = 'João';
        $userInfo->cpf = '950.296.359-80';
        $userInfo->birthdate = '1990-08-23';

        $user = new User();
        $user->fill($userInfo);

        $this->assertEquals($userInfo->id, $user->getId());
        $this->assertEquals($userInfo->name, $user->getName());
        $this->assertEquals($userInfo->cpf, $user->getCpf());
        $this->assertEquals($userInfo->birthdate, $user->getBirthdate());
    }
}
