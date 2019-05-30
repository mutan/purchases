<?php

namespace App\Entity\Traits;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

// !!! Don't forget to add into entity class notation: @ORM\HasLifecycleCallbacks

trait TimestampableEntityTrait
{
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $createDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updateDate;

    public function getCreateDate(): ?DateTimeInterface
    {
        return $this->createDate;
    }

    public function setCreateDate(?DateTimeInterface $createDate): self
    {
        $this->createDate = $createDate;
        return $this;
    }

    public function getUpdateDate(): ?DateTimeInterface
    {
        return $this->updateDate;
    }

    public function setUpdateDate(?DateTimeInterface $updateDate): self
    {
        $this->updateDate = $updateDate;
        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps(): self
    {
        $this->setUpdateDate(new DateTime('now'));
        if ($this->getCreateDate() == null) {
            $this->setCreateDate(new DateTime('now'));
        }
        return $this;
    }
}
