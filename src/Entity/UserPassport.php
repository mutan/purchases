<?php

namespace App\Entity;

use App\Entity\Interfaces\PrefixableEntityInterface;
use App\Entity\Traits\TimestampableEntityTrait;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserPassportRepository")
 * @ORM\HasLifecycleCallbacks
 */
class UserPassport implements PrefixableEntityInterface
{
    use TimestampableEntityTrait;

    const PREFIX = 'UP'; // User Passport

    const STATUS_NEW = 'new';
    const STATUS_APPROVED = 'approved';
    const STATUS_DELETED = 'deleted';

    const ALLOWED_STATUSES = [
        self::STATUS_NEW,
        self::STATUS_APPROVED,
        self::STATUS_DELETED,
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userPassports")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, options={"comment":"Серия"})
     * @Assert\NotBlank(message="~not_blank")
     * @Assert\Regex(pattern="/^\d{4}$/", message="~regexp.series")
     */
    private $series;

    /**
     * @ORM\Column(type="string", length=255, options={"comment":"Номер"})
     * @Assert\NotBlank(message="~not_blank")
     * @Assert\Regex(pattern="/^\d{6}$/", message="~regexp.number")
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=255, options={"comment":"Кем выдан"})
     * @Assert\NotBlank(message="~not_blank")
     */
    private $giveBy;

    /**
     * @ORM\Column(type="date", options={"comment":"Дата выдачи"})
     * @Assert\NotBlank(message="~not_blank")
     * @Assert\Date
     */
    private $giveDate;

    /**
     * @ORM\Column(type="date", options={"comment":"Дата рождения"})
     * @Assert\NotBlank(message="~not_blank")
     * @Assert\Date
     */
    private $birthDate;

    /**
     * @ORM\Column(type="string", length=255, options={"comment":"ИНН"})
     * @Assert\NotBlank(message="~not_blank")
     * @Assert\Regex(pattern="/^\d{12}$/", message="~regexp.inn")
     */
    private $inn;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status = self::STATUS_NEW;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeries(): ?string
    {
        return $this->series;
    }

    public function setSeries(string $series): self
    {
        $this->series = $series;
        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;
        return $this;
    }

    public function getGiveBy(): ?string
    {
        return $this->giveBy;
    }

    public function setGiveBy(string $giveBy): self
    {
        $this->giveBy = $giveBy;
        return $this;
    }

    public function getGiveDate(): ?DateTimeInterface
    {
        return $this->giveDate;
    }

    public function setGiveDate(?DateTimeInterface $giveDate): self
    {
        $this->giveDate = $giveDate;
        return $this;
    }

    public function getBirthDate(): ?DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function getInn(): ?string
    {
        return $this->inn;
    }

    public function setInn(string $inn): self
    {
        $this->inn = $inn;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, self::ALLOWED_STATUSES)) {
            throw new InvalidArgumentException("Invalid user_passport status");
        }

        $this->status = $status;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /* ADDITIONAL METHODS */

    public function getPrefix(): string
    {
        return self::PREFIX;
    }

    public function getIdWithPrefix(): string
    {
        return $this->getPrefix() . $this->getId();
    }

    public function __toString()
    {
        return $this->getIdWithPrefix();
    }

    public function isNew()
    {
        return $this->getStatus() == self::STATUS_NEW;
    }

    public function isApproved()
    {
        return $this->getStatus() == self::STATUS_APPROVED;
    }

    public function isDeleted()
    {
        return $this->getStatus() == self::STATUS_DELETED;
    }
}
