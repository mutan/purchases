<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserAddressRepository")
 */
class UserAddress
{
    use TimestampableTrait;

    const STATUS_NEW      = 'new';
    const STATUS_APPROVED = 'approved';
    const STATUS_DELETED  = 'deleted';

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
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userAddresses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, options={"comment":"Фамилия"})
     * @Assert\NotBlank(message="~not_blank")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, options={"comment":"Имя"})
     * @Assert\NotBlank(message="~not_blank")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"comment":"Отчество"})
     */
    private $middleName;

    /**
     * @ORM\Column(type="string", length=255, options={"comment":"Страна"})
     * @Assert\NotBlank(message="~not_blank")
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255, options={"comment":"Почтовый идекс"})
     * @Assert\NotBlank(message="~not_blank")
     */
    private $postCode;

    /**
     * @ORM\Column(type="string", length=255, options={"comment":"Регион"})
     * @Assert\NotBlank(message="~not_blank")
     */
    private $region;

    /**
     * @ORM\Column(type="string", length=255, options={"comment":"Город"})
     * @Assert\NotBlank(message="~not_blank")
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, options={"comment":"Улица"})
     * @Assert\NotBlank(message="~not_blank")
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=255, options={"comment":"Дом"})
     * @Assert\NotBlank(message="~not_blank")
     */
    private $house;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"comment":"Строение или корпус"})
     */
    private $building;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"comment":"Квартира"})
     */
    private $flat;

    /**
     * @ORM\Column(type="string", length=255, options={"comment":"Телефон"})
     * @Assert\NotBlank(message="~not_blank")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="~not_blank")
     * @Assert\Email(message="Введите существующий емейл.")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status = self::STATUS_NEW;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): self
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function setPostCode(?string $postCode): self
    {
        $this->postCode = $postCode;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getHouse(): ?string
    {
        return $this->house;
    }

    public function setHouse(?string $house): self
    {
        $this->house = $house;

        return $this;
    }

    public function getBuilding(): ?string
    {
        return $this->building;
    }

    public function setBuilding(?string $building): self
    {
        $this->building = $building;

        return $this;
    }

    public function getFlat(): ?string
    {
        return $this->flat;
    }

    public function setFlat(?string $flat): self
    {
        $this->flat = $flat;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, self::ALLOWED_STATUSES)) {
            throw new \InvalidArgumentException("Invalid user_address status");
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

    public function __toString()
    {
        return $this->getIdWithPrefix();
    }

    public function getIdWithPrefix()
    {
        return 'UA' . $this->id;
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
