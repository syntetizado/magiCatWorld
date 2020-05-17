<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductTb
 *
 * @ORM\Table(name="product_tb", indexes={@ORM\Index(name="producttb_categoryfk", columns={"id_category"})})
 * @ORM\Entity
 */
class ProductTb
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=60, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="price", type="decimal", precision=65, scale=2, nullable=true)
     */
    private $price;

    /**
     * @var string|null
     *
     * @ORM\Column(name="image", type="string", length=60, nullable=true)
     */
    private $image;

    /**
     * @var int|null
     *
     * @ORM\Column(name="warehouse_quantity", type="integer", nullable=true)
     */
    private $warehouseQuantity;

    /**
     * @var string|null
     *
     * @ORM\Column(name="slug", type="string", length=60, nullable=true)
     */
    private $slug;

    /**
     * @var int|null
     *
     * @ORM\Column(name="active", type="integer", nullable=true)
     */
    private $active;

    /**
     * @var \CategoryTb
     *
     * @ORM\ManyToOne(targetEntity="CategoryTb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_category", referencedColumnName="id")
     * })
     */
    private $category;

    private $child;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

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

    public function getWarehouseQuantity(): ?int
    {
        return $this->warehouseQuantity;
    }

    public function setWarehouseQuantity(?int $warehouseQuantity): self
    {
        $this->warehouseQuantity = $warehouseQuantity;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getActive(): ?int
    {
        return $this->active;
    }

    public function setActive(?int $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getCategory(): ?CategoryTb
    {
        return $this->category;
    }

    public function setIdCategory(?CategoryTb $category): self
    {
        $this->category = $category;

        return $this;
    }


}
