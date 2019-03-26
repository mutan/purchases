<?php

namespace App\Entity;

use App\Entity\Traits\UserTokenTrait;
use Doctrine\ORM\Mapping as ORM;

// TODO переделать в ManyToOne + UNIQUE на поле user_id

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserProfileRepository")
 * @ORM\HasLifecycleCallbacks
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

    /* ADDITIONAL METHODS */

}
