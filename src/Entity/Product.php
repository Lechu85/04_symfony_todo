<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 *
 * @ApiResource(
 *   collectionOperations={"get"={"normalization_context"={"groups"="product:list"}}},
 *   itemOperations={"get"={"normalization_context"={"groups"="product:item"}}},
 *   order={"name"="DESC", "id"="ASC"},
 *   paginationEnabled=false
 * )
 */



class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("product:list", "product:item")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("product:list", "product:item")
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups("product:list", "product:item")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("product:list", "product:item")
     */
    private $shortDescription;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Groups("product:list", "product:item")
     */
    private $priceNet1;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Groups("product:list", "product:item")
     */
    private $priceNet2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("product:list", "product:item")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=150, unique=true)
     * @Groups("product:list", "product:item")
     */
    private $slug;

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

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getPriceNet1(): ?string
    {
        return $this->priceNet1;
    }

    public function setPriceNet1(string $priceNet1): self
    {
        $this->priceNet1 = $priceNet1;

        return $this;
    }

    public function getPriceNet2(): ?string
    {
        return $this->priceNet2;
    }

    public function setPriceNet2(string $priceNet2): self
    {
        $this->priceNet2 = $priceNet2;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
