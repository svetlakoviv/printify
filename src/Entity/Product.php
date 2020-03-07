<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20, unique=true)
     */
    private $SKU;

    /**
     * @ORM\Column(type="integer")
     */
    private $cost;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductType", inversedBy="product")
     */
    private $productType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="product")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderProduct", mappedBy="product", orphanRemoval=true)
     */
    private $orders;

    public function __construct()
    {
        $this->productType = new ProductType();
        $this->user = new User();
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSKU(): ?string
    {
        return $this->SKU;
    }

    public function setSKU(string $SKU): self
    {
        $this->SKU = $SKU;

        return $this;
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function setCost(int $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getProductType(): ProductType
    {
        return $this->productType;
    }

    public function setProductType(?ProductType $productType): self
    {
        $this->productType = $productType;

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

    /**
     * @return Collection|OrderProduct[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(OrderProduct $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setProduct($this);
        }

        return $this;
    }

    public function removeOrder(OrderProduct $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getProduct() === $this) {
                $order->setProduct(null);
            }
        }

        return $this;
    }

    public function asArray()
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'cost' => $this->getCost(),
            'type' => $this->productType->getName(),
            'user' => $this->user->getName()
        ];
    }
}
