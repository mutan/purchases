<?php

namespace App\Entity;

use App\Entity\Traits\UserTokenTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

// TODO переделать в ManyToOne + UNIQUE на поле user_id

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserProfileRepository")
 * @UniqueEntity("user")
 */
class UserProfile
{
    use UserTokenTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLoginDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userProfile")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastLoginDate(): ?\DateTimeInterface
    {
        return $this->lastLoginDate;
    }

    public function setLastLoginDate(?\DateTimeInterface $lastLoginDate): self
    {
        $this->lastLoginDate = $lastLoginDate;

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

}
