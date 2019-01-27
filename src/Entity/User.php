<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity("email")
 * @UniqueEntity("name")
 */
class User implements UserInterface
{
    use UserTokenTrait;
    use TimestampableEntityTrait;

    const ROLE_USER       = 'ROLE_USER';
    const ROLE_MODERATOR  = 'ROLE_MODERATOR';
    const ROLE_ADMIN      = 'ROLE_ADMIN';

    const ALLOWED_ROLES = [
        self::ROLE_USER,
        self::ROLE_MODERATOR,
        self::ROLE_ADMIN,
    ];

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    const ALLOWED_STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    const INACTIVE_REASON_BANNED        = 'banned';
    const INACTIVE_REASON_NOT_ACTIVATED = 'not_activated';

    const ALLOWED_INACTIVE_REASONS = [
        self::INACTIVE_REASON_BANNED,
        self::INACTIVE_REASON_NOT_ACTIVATED,
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserProfile", mappedBy="user", cascade={"persist", "remove"})
     */
    private $userProfile;

    /**
     * @ORM\Column(type="string", length=30, unique=true)
     * @Assert\NotBlank(message="Имя пользователя не может быть пустым")
     * @Assert\Regex(pattern="/^\w+$/", message="Имя пользователя может содержать только буквы, цифры и знак подчеркивания.")
     * @Assert\Length(
     *     min=3,
     *     max=30,
     *     minMessage="Имя должно состоять минимум из {{ limit }} символов.",
     *     maxMessage="Имя должно состоять максимум из {{ limit }} символов."
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\NotBlank(message="Емейл не может быть пустым.", groups={"forgot_password"})
     * @Assert\Email(message="Введите существующий емейл.", groups={"forgot_password"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [self::ROLE_USER];

    /**
     * @Assert\NotBlank(message="Пароль не может быть пустым.", groups={"reset_password"})
     * @AppAssert\ComplexPassword(message="Пароль должен содержать цифры и латинские буквы в нижнем и верхнем регистре.", groups={"reset_password"})
     * @Assert\Length(
     *     min=8,
     *     max=4096,
     *     minMessage="Пароль должен состоять минимум из {{ limit }} символов.",
     *     maxMessage="Пароль должен состоять максимум из {{ limit }} символов.",
     *     groups={"reset_password"}
     * )
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, options={"default" = User::STATUS_INACTIVE})
     */
    private $status = self::STATUS_INACTIVE;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"default" = User::INACTIVE_REASON_NOT_ACTIVATED})
     */
    private $inactive_reason = self::INACTIVE_REASON_NOT_ACTIVATED;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLoginAt;

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using bcrypt or argon
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function isActive()
    {
        return $this->getStatus() == self::STATUS_ACTIVE;
    }

    public function isBanned()
    {
        return $this->getStatus()         == self::STATUS_INACTIVE
            && $this->getInactiveReason() == self::INACTIVE_REASON_BANNED;
    }

    public function isNotActivated()
    {
        return $this->getStatus()         == self::STATUS_INACTIVE
            && $this->getInactiveReason() == self::INACTIVE_REASON_NOT_ACTIVATED;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = self::ROLE_USER;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        foreach ($roles as $role) {
            if (!in_array($role, self::ALLOWED_ROLES)) {
                throw new \InvalidArgumentException("Invalid user roles");
            }
        }

        $this->roles = $roles;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, self::ALLOWED_STATUSES)) {
            throw new \InvalidArgumentException("Invalid user status");
        }

        $this->status = $status;

        return $this;
    }

    public function getInactiveReason(): ?string
    {
        return $this->inactive_reason;
    }

    public function setInactiveReason(?string $inactive_reason): self
    {
        if (!in_array($inactive_reason, self::ALLOWED_INACTIVE_REASONS)) {
            throw new \InvalidArgumentException("Invalid user inactive reason");
        }

        $this->inactive_reason = $inactive_reason;

        return $this;
    }

    public function clearInactiveReason(): self
    {
        $this->inactive_reason = null;

        return $this;
    }

    public function getLastLoginAt(): ?\DateTimeInterface
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?\DateTimeInterface $lastLoginAt): self
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    public function getUserProfile(): ?UserProfile
    {
        return $this->userProfile;
    }

    public function setUserProfile(UserProfile $userProfile): self
    {
        $this->userProfile = $userProfile;

        // set the owning side of the relation if necessary
        if ($this !== $userProfile->getUser()) {
            $userProfile->setUser($this);
        }

        return $this;
    }
}
