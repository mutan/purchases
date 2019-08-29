<?php

namespace App\Entity;

use DateTimeInterface;
use InvalidArgumentException;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\UserTokenTrait;
use Doctrine\Common\Collections\Collection;
use App\Validator\Constraints as AppAssert;
use App\Entity\Traits\TimestampableEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Interfaces\PrefixableEntityInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="`user`")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity("email")
 */
class User implements UserInterface, PrefixableEntityInterface
{
    use UserTokenTrait;
    use TimestampableEntityTrait;

    const PREFIX = 'U'; // User

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const ALLOWED_STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    const INACTIVE_REASON_BANNED = 'banned'; // banned by admin (for any reason, including user's request)
    const INACTIVE_REASON_NOT_ACTIVATED = 'not_activated'; // not activated after registration

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
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\NotBlank(message="Емейл не может быть пустым.", groups={"forgot_password"})
     * @Assert\Email(message="Введите существующий емейл.", groups={"forgot_password"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, options={"comment":"Фамилия"})
     * @Assert\NotBlank(message="Фамилия не может быть пустой")
     * @Assert\Regex(pattern="/^\W+$/", message="Никнейм может содержать только буквы")
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, options={"comment":"Имя"})
     * @Assert\NotBlank(message="Имя не может быть пустым")
     * @Assert\Regex(pattern="/^\W+$/", message="Никнейм может содержать только буквы")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"comment":"Отчество"})
     * @Assert\Regex(pattern="/^\W+$/", message="Никнейм может содержать только буквы")
     */
    private $middlename;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"comment":"Ссылка на аккаунт Вконтакте"})
     */
    private $vk;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"comment":"Ссылка на Телеграм аккаунт"})
     */
    private $telegram;

    /**
     * @Assert\NotBlank(message="Пароль не может быть пустым.", groups={"reset_password"})
     * @AppAssert\AlphanumericPassword(message="Пароль должен содержать цифры, а также латинские буквы в нижнем и верхнем регистре.", groups={"reset_password"})
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
     * @ORM\OneToMany(targetEntity="App\Entity\UserAddress", mappedBy="user")
     */
    private $userAddresses;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserPassport", mappedBy="user")
     */
    private $userPassports;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="user", orphanRemoval=true)
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="user", orphanRemoval=true)
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="manager", orphanRemoval=true)
     */
    private $ordersByManager;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLoginDate;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\UserRole", inversedBy="users")
     */
    private $roles;

    public function __construct()
    {
        $this->userAddresses = new ArrayCollection();
        $this->userPassports = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->ordersByManager = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getMiddlename(): ?string
    {
        return $this->middlename;
    }

    public function setMiddlename(string $middlename): self
    {
        $this->middlename = $middlename;
        return $this;
    }

    public function setVk(?string $vk): self
    {
        $this->vk = $vk;
        return $this;
    }

    public function getVk(): ?string
    {
        return $this->vk;
    }

    public function setTelegram(?string $telegram): self
    {
        $this->telegram = $telegram;
        return $this;
    }

    public function getTelegram(): ?string
    {
        return $this->telegram;
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
            throw new InvalidArgumentException("Invalid user status");
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
            throw new InvalidArgumentException("Invalid user inactive reason");
        }
        $this->inactive_reason = $inactive_reason;
        return $this;
    }

    public function clearInactiveReason(): self
    {
        $this->inactive_reason = null;
        return $this;
    }

    public function getLastLoginDate(): ?DateTimeInterface
    {
        return $this->lastLoginDate;
    }

    public function setLastLoginDate(?DateTimeInterface $lastLoginDate): self
    {
        $this->lastLoginDate = $lastLoginDate;
        return $this;
    }

    /**
     * @return Collection|UserAddress[]
     */
    public function getUserAddresses(): Collection
    {
        return $this->userAddresses;
    }

    public function addUserAddress(UserAddress $userAddress): self
    {
        if (!$this->userAddresses->contains($userAddress)) {
            $this->userAddresses[] = $userAddress;
            $userAddress->setUser($this);
        }
        return $this;
    }

    public function removeUserAddress(UserAddress $userAddress): self
    {
        if ($this->userAddresses->contains($userAddress)) {
            $this->userAddresses->removeElement($userAddress);
            // set the owning side to null (unless already changed)
            if ($userAddress->getUser() === $this) {
                $userAddress->setUser(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|UserPassport[]
     */
    public function getUserPassports(): Collection
    {
        return $this->userPassports;
    }

    public function addUserPassport(UserPassport $userPassport): self
    {
        if (!$this->userPassports->contains($userPassport)) {
            $this->userPassports[] = $userPassport;
            $userPassport->setUser($this);
        }
        return $this;
    }

    public function removeUserPassport(UserPassport $userPassport): self
    {
        if ($this->userPassports->contains($userPassport)) {
            $this->userPassports->removeElement($userPassport);
            // set the owning side to null (unless already changed)
            if ($userPassport->getUser() === $this) {
                $userPassport->setUser(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setUser($this);
        }
        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getUser() === $this) {
                $product->setUser(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setUser($this);
        }
        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrdersByManager(): Collection
    {
        return $this->ordersByManager;
    }

    public function addOrdersByManager(Order $ordersByManager): self
    {
        if (!$this->ordersByManager->contains($ordersByManager)) {
            $this->ordersByManager[] = $ordersByManager;
            $ordersByManager->setManager($this);
        }
        return $this;
    }

    public function removeOrdersByManager(Order $ordersByManager): self
    {
        if ($this->ordersByManager->contains($ordersByManager)) {
            $this->ordersByManager->removeElement($ordersByManager);
            // set the owning side to null (unless already changed)
            if ($ordersByManager->getManager() === $this) {
                $ordersByManager->setManager(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserRole[]
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(UserRole $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(UserRole $role): self
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
        }
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

    public function getFullName(): string
    {
        return $this->getLastname() . ' ' . $this->getFirstname();
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
}
