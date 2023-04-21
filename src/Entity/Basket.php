<?php

namespace App\Entity;

use App\Repository\BasketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BasketRepository::class)]
class Basket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'baskets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $purchaseDate = null;

    #[ORM\Column]
    private ?bool $state = null;

    #[ORM\OneToMany(mappedBy: 'basket', targetEntity: ContentBasket::class)]
    private Collection $contentBaskets;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPurchaseDate(): ?\DateTimeInterface
    {
        return $this->purchaseDate;
    }

    public function setPurchaseDate(\DateTimeInterface $purchaseDate): self
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    public function isState(): ?bool
    {
        return $this->state;
    }

    public function setState(bool $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->state = false;
        $this->contentBaskets = new ArrayCollection();
    }

    /**
     * @return Collection<int, ContentBasket>
     */
    public function getContentBaskets(): Collection
    {
        return $this->contentBaskets;
    }

    public function addContentBasket(ContentBasket $contentBasket): self
    {
        if (!$this->contentBaskets->contains($contentBasket)) {
            $this->contentBaskets->add($contentBasket);
            $contentBasket->setBasket($this);
        }

        return $this;
    }

    public function removeContentBasket(ContentBasket $contentBasket): self
    {
        if ($this->contentBaskets->removeElement($contentBasket)) {
            // set the owning side to null (unless already changed)
            if ($contentBasket->getBasket() === $this) {
                $contentBasket->setBasket(null);
            }
        }

        return $this;
    }
}
