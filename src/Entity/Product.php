<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[Vich\Uploadable]
#[UniqueEntity(fields: ['name'], message: 'Ce produit existe déjà')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    #[Assert\Length(
        max: 10000,
        maxMessage: 'Le prix ne peut pas dépasser 10 000 €'
    )]

    private ?float $price = null;

    #[ORM\Column]
    private ?int $supply = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[Vich\UploadableField(mapping: 'products', fileNameProperty: 'image')]
    private ?File $imageFile = null;

    #[ORM\OneToMany(mappedBy: 'products', targetEntity: ContentBasket::class)]
    private Collection $contentBaskets;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->contentBaskets = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getSupply(): ?int
    {
        return $this->supply;
    }

    public function setSupply(int $supply): self
    {
        $this->supply = $supply;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): self
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    // #[ORM\PostRemove] //on ajoute cette annotation pour dire que cette méthode doit être appelée après la suppression d'un produit
    // public function deleteImage(){
    //     //Si le produit possède une image
    //     if($this->image != null){
    //         //on le supprime
    //         unlink(__DIR__.'/../../public/uploads/'.$this->image);
    //     }

    //     return true; //on retourne true pour dire que tout s'est bien passé
    // }

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
            $contentBasket->setProducts($this);
        }

        return $this;
    }

    public function removeContentBasket(ContentBasket $contentBasket): self
    {
        if ($this->contentBaskets->removeElement($contentBasket)) {
            // set the owning side to null (unless already changed)
            if ($contentBasket->getProducts() === $this) {
                $contentBasket->setProducts(null);
            }
        }

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    
}
