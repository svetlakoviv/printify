<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $balance;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="user", orphanRemoval=true)
     */
    private $product;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PrintingOrder", mappedBy="user", orphanRemoval=true)
     */
    private $order;

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

    public function getBalance(): ?int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function getProduct(): ?Collection
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getOrder(): ?Collection
    {
        return $this->order;
    }

    public function setOrder(?OrderProduct $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function asArray()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'balance' => $this->getBalance()
        ];
    }
}
