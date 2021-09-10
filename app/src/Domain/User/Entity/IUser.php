<?php 

declare(strict_types=1);

namespace BankingAccount\Domain\User\Entity;

use stdClass;

/**
 * @codeCoverageIgnore
 */
interface IUser
{
    public function getId(): ?int;
    public function setId(int $id): void;

    public function getName(): string;
    public function setName(string $name): void;

    public function getCpf(): string;
    public function setCpf(string $cpf): void;

    public function getBirthdate(): string;
    public function setBirthdate(string $birthdate): void;
    public function getBirthdateBr(): string;

    public function fill(stdClass $userInfo): void;
}
