<?php

namespace App\Entity\Interfaces;

interface PrefixableEntityInterface
{
    public function getId(): ?int;
    public function getPrefix(): string;
    public function getIdWithPrefix(): string;
    public function __toString();
}
