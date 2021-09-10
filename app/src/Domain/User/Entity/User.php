<?php 

declare(strict_types=1);

namespace BankingAccount\Domain\User\Entity;

use BankingAccount\Helper\ErrorHandler;
use BankingAccount\Helper\HttpStatus;
use BankingAccount\Helper\Cpf;
use DateTime;
use InvalidArgumentException;
use JsonSerializable;
use stdClass;

class User implements IUser, JsonSerializable
{
    private int $id;
    private string $name;
    private string $cpf;
    private string $birthdate;

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
            throw new InvalidArgumentException('ID inválido!', HttpStatus::CODE_422);
        }
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        if (! filter_var($name, FILTER_SANITIZE_STRING) || empty(trim($name))) {
            throw new InvalidArgumentException('Nome inválido, preencha o campo corretamente!', HttpStatus::CODE_422);
        }
        $this->name = $name;
    }

    public function getCpf(): string
    {
       return $this->cpf;
    }

    public function setCpf(?string $cpf): void
    {
        if (! Cpf::validate($cpf)) {
            throw new InvalidArgumentException('CPF inválido, preencha o campo corretamente!', HttpStatus::CODE_422);
        }
        $this->cpf = Cpf::doMask($cpf);
    }

    public function getBirthdate(): string
    {
        return $this->birthdate;
    }

    public function setBirthdate(?string $birthdate): void
    {
        $birthdate = $birthdate ?? '';
        $format = 'Y-m-d';
        if (strstr($birthdate, '/') !== false) {
            $format = 'd/m/Y';
        }
        
        $birthdate = DateTime::createFromFormat($format, $birthdate);
        if (! $birthdate) {
            $message = ErrorHandler::INVALID_USER_BIRTHDATE_MESSAGE;
            throw new InvalidArgumentException($message, HttpStatus::CODE_422);
        }

        $this->birthdate = $birthdate->format('Y-m-d');
    }

    public function getBirthdateBr(): string
    {
        $birthdate = DateTime::createFromFormat('Y-m-d', $this->birthdate);
        return $birthdate->format('d/m/Y');
    }

    public function fill(stdClass $userInfo): void
    {
        if (isset($userInfo->id)) {
            $this->setId((int) $userInfo->id);
        }

        $this->setName($userInfo->name);
        $this->setCpf($userInfo->cpf);
        $this->setBirthdate($userInfo->birthdate);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'cpf' => $this->getCpf(),
            'birthdate' => $this->getBirthdate(),
            'birthdate_br' => $this->getBirthdateBr(),
        ];
    }

}
